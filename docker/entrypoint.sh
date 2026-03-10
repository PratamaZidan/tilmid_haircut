#!/bin/sh
set -e

echo "Running migrations..."
php /var/www/html/artisan migrate --force

echo "Checking if seeding is needed..."
USER_COUNT=$(php /var/www/html/artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -n1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "No users found, running seeders..."
    php /var/www/html/artisan db:seed --force
else
    echo "Users already exist ($USER_COUNT found), skipping seeder."
fi

echo "Caching config & routes..."
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache

echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf