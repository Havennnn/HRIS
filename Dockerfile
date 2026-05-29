# ─── Stage 1: Build frontend assets ──────────────────────────────────────────
FROM node:22-alpine AS frontend

WORKDIR /build
COPY package-lock.json package.json ./
RUN npm ci
COPY . .
RUN npm run build

# ─── Stage 2: PHP runtime ────────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS runtime

# Install system deps + PHP extensions
RUN apk add --no-cache nginx supervisor bash curl \
    && docker-php-ext-install pcntl pdo_pgsql pgsql \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app
COPY --from=frontend /build /var/www/html
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Nginx config
COPY nginx.conf /etc/nginx/http.d/default.conf

# Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

ENTRYPOINT ["/docker-entrypoint.sh"]
