#!/bin/sh
set -eu

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY must be set in the container environment." >&2
    exit 1
fi

mkdir -p storage/app/public storage/app/private \
    storage/framework/cache/data storage/framework/sessions \
    storage/framework/views storage/logs bootstrap/cache \
    public/uploads/settings public/uploads/editor public/uploads/products
chown www-data:www-data \
    storage storage/app storage/app/public storage/app/private \
    storage/framework storage/framework/cache storage/framework/cache/data \
    storage/framework/sessions storage/framework/views storage/logs \
    bootstrap/cache public/uploads public/uploads/settings \
    public/uploads/editor public/uploads/products

exec "$@"
