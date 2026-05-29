#!/bin/sh
set -e

cd /var/www/html

# Generate app key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:..." ]; then
    php artisan key:generate --force
fi

# Run migrations on startup
php artisan migrate --force --isolated 2>/dev/null || php artisan migrate --force

# Cache for production
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true
php artisan event:cache 2>/dev/null || true

# Start supervisor (manages nginx + php-fpm + queue + ssr + scheduler)
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
