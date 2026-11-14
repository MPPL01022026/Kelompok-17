#!/bin/sh
set -eu

cd /var/www/html

# Ensure writable storage and bootstrap cache directories exist
mkdir -p \
    storage/app/public/products \
    storage/app/public/payment \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

# Ensure APP_KEY is set (generate persistent container fallback if omitted)
if [ -z "${APP_KEY:-}" ]; then
    KEY_FILE="storage/framework/.generated_app_key"
    if [ ! -f "$KEY_FILE" ]; then
        php -r "echo 'base64:' . base64_encode(random_bytes(32));" > "$KEY_FILE"
        chmod 600 "$KEY_FILE"
    fi
    APP_KEY="$(cat "$KEY_FILE")"
    export APP_KEY
    echo "[entrypoint] Notice: APP_KEY was empty; using generated key in $KEY_FILE. Set APP_KEY explicitly in production." >&2
fi

# Wait for MySQL when DB_CONNECTION=mysql
if [ "${DB_CONNECTION:-mysql}" = "mysql" ]; then
    DB_HOST="${DB_HOST:-mysql}"
    DB_PORT="${DB_PORT:-3306}"
    echo "[entrypoint] Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
    tries=0
    until php -r "exit(@fsockopen('${DB_HOST}', (int) '${DB_PORT}', \$e, \$s, 2) ? 0 : 1);" 2>/dev/null; do
        tries=$((tries + 1))
        if [ "$tries" -ge 30 ]; then
            echo "[entrypoint] Error: MySQL did not become reachable within 60s." >&2
            exit 1
        fi
        sleep 2
    done
    echo "[entrypoint] MySQL is reachable."
fi

# Ensure public/storage symlink exists
php artisan storage:link --force || true

# Run database migrations when enabled
if [ "${AUTORUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

# Optionally run database seeders (disabled by default in production)
if [ "${AUTORUN_SEED:-false}" = "true" ]; then
    php artisan db:seed --class=DatabaseSeeder --force --no-interaction
fi

# Refresh Laravel caches with live runtime environment variables
php artisan optimize:clear --no-interaction || true
php artisan optimize --no-interaction

chown -R www-data:www-data storage bootstrap/cache

if [ "$#" -gt 0 ]; then
    exec "$@"
fi

php-fpm -D
exec nginx -g "daemon off;"

