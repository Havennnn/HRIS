# ─── Stage 1: PHP vendor dependencies ──────────────────────────────────────
# Installs composer deps so the piacore Vite plugin (vendor JS files) is
# available for the frontend build stage. Piacore is a VCS repo on GitHub,
# so composer installs it naturally even when Package/piacore isn't present.
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-req=ext-pgsql \
    --ignore-platform-req=ext-pdo_pgsql

# ─── Stage 2: Build frontend ───────────────────────────────────────────────
FROM node:22-alpine AS frontend

WORKDIR /build

COPY package.json package-lock.json ./
RUN npm ci

# Copy the piacore Vite plugin from the composer stage so Vite can resolve
# the import in vite.config.ts (./vendor/latsmarbls/piacore/resources/js/...)
COPY --from=vendor /app/vendor/latsmarbls/piacore/resources/js \
     ./vendor/latsmarbls/piacore/resources/js

COPY . .
RUN npm run build

# ─── Stage 3: PHP runtime ──────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS runtime

RUN apk add --no-cache nginx supervisor bash curl \
    && docker-php-ext-install pcntl pdo_pgsql pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Application source + vendor from composer stage
COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN composer run-script post-autoload-dump 2>/dev/null || true

# Built frontend assets
COPY --from=frontend /build/public/build ./public/build

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache

# Nginx + Supervisor
RUN rm -f /etc/nginx/conf.d/default.conf
COPY nginx.conf /etc/nginx/http.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/docker-entrypoint.sh"]
