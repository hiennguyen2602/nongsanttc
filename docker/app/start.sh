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

migration_attempts="${DB_MIGRATION_ATTEMPTS:-30}"
migration_retry_seconds="${DB_MIGRATION_RETRY_SECONDS:-2}"
migration_attempt=1

until php artisan migrate --force --no-interaction; do
    if [ "$migration_attempt" -ge "$migration_attempts" ]; then
        echo "Database migration failed after ${migration_attempts} attempts. Check DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME and DB_PASSWORD." >&2
        exit 1
    fi

    echo "Database is not ready; retrying migration (${migration_attempt}/${migration_attempts})..." >&2
    migration_attempt=$((migration_attempt + 1))
    sleep "$migration_retry_seconds"
done

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf