FROM php:8.2-fpm-alpine

# Install system packages
RUN apk add --no-cache nginx supervisor bash curl nodejs npm \
    && docker-php-ext-install pcntl pdo_pgsql pgsql \
    && npm install -g npm@latest 2>/dev/null || true

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Install PHP deps
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader --no-scripts

# Build frontend
RUN npm ci && npm run build

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache

# Cleanup
RUN rm -rf node_modules

# Run post-install scripts now that everything is in place
RUN composer run-script post-autoload-dump 2>/dev/null || true

# Nginx config
RUN rm -f /etc/nginx/conf.d/default.conf
COPY nginx.conf /etc/nginx/http.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/docker-entrypoint.sh"]
