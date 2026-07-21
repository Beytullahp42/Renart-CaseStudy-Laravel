#!/bin/sh
set -e

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    export APP_KEY="$(php -r 'echo "base64:" . base64_encode(random_bytes(32));')"
    echo "Generated an ephemeral APP_KEY for this container. Set RENART_APP_KEY for a stable production key."
fi

mkdir -p storage/app/private storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache

if [ ! -f storage/app/private/products.json ] && [ -f /usr/local/share/renart/products.json ]; then
    cp /usr/local/share/renart/products.json storage/app/private/products.json
fi

if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache
fi

php artisan config:clear --no-interaction
php artisan route:clear --no-interaction
php artisan view:clear --no-interaction
php artisan event:clear --no-interaction

php artisan storage:link --quiet || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

if [ "${CACHE_CONFIG:-true}" = "true" ]; then
    php artisan config:cache --no-interaction
fi

if [ "${CACHE_VIEWS:-true}" = "true" ]; then
    php artisan view:cache --no-interaction
fi

if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache
fi

exec "$@"
