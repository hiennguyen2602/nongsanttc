#!/bin/sh
set -eu

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY must be set in the container environment." >&2
    exit 1
fi

mkdir -p storage/app/public storage/app/private \
    storage/framework/cache storage/framework/sessions \
    storage/framework/views storage/logs bootstrap/cache \
    public/uploads/settings public/uploads/editor public/uploads/products
chown -R www-data:www-data storage bootstrap/cache public/uploads

php artisan migrate --force --no-interaction

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf