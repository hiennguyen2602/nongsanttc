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

app_port="${PORT:-80}"
case "$app_port" in
    ''|*[!0-9]*)
        echo "PORT must be a numeric TCP port; received: ${app_port}" >&2
        exit 1
        ;;
esac
sed -i "s/listen 80 default_server;/listen ${app_port} default_server;/" /etc/nginx/conf.d/default.conf

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf &
supervisor_pid=$!

if [ "${RUN_MIGRATIONS_ON_STARTUP:-false}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

wait "$supervisor_pid"