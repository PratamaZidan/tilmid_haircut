#!/bin/sh
set -e

echo "==> Running migrations..."
php artisan migrate --force --isolated

echo "==> Linking storage..."
php artisan storage:link --force

echo "==> Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf