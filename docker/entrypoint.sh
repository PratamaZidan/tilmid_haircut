#!/bin/sh
set -e

echo "Running migrations..."
php /var/www/html/artisan migrate --force

echo "Caching config & routes..."
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache

echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf