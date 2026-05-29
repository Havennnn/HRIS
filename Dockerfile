# ─── Stage 1: Build frontend ──────────────────────────────────────────────
FROM node:22-alpine AS frontend
ARG PIACORE_VERSION

# Why download piacore JS from GitHub?
# The vite.config.ts imports ./vendor/latsmarbls/piacore/resources/js/vite-plugin-piacore
# and many app files import piacore/... components (resolve.alias points to that dir).
# /vendor is gitignored, so these files aren't in the checkout.
# Downloading them directly from the piacore release is more reliable than
# running composer install in a separate stage (avoids PHP platform req issues).
ARG PIACORE_VERSION=v1.2.1

WORKDIR /build

# Install npm dependencies first for layer caching
COPY package.json package-lock.json ./
RUN npm ci

# Download piacore JS files from GitHub and place them at the expected path
RUN curl -sL "https://github.com/LatsMarbls/piacore/archive/refs/tags/${PIACORE_VERSION}.tar.gz" \
    | tar -xz -C /tmp \
    && mkdir -p vendor/latsmarbls/piacore/resources/js \
    && cp -r /tmp/piacore-*/resources/js/* vendor/latsmarbls/piacore/resources/js/ \
    && rm -rf /tmp/piacore-*

# Build the application
COPY . .
RUN npm run build

# ─── Stage 2: PHP runtime ─────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS runtime

RUN apk add --no-cache nginx supervisor bash curl \
    && docker-php-ext-install pcntl pdo_pgsql pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Install PHP dependencies (composer downloads piacore from GitHub VCS repo)
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader --no-scripts \
    && composer run-script post-autoload-dump 2>/dev/null || true

# Copy built frontend assets
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
