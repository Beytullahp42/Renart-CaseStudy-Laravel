#!/bin/sh
set -e

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    export APP_KEY="$(php -r 'echo "base64:" . base64_encode(random_bytes(32));')"
    echo "Generated an ephemeral APP_KEY for this container. Set RENART_APP_KEY for a stable production key."
fi

mkdir -p storage/app/private storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

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

if [ "${WAIT_FOR_DB:-true}" = "true" ] && [ "${DB_CONNECTION:-pgsql}" = "pgsql" ]; then
    php <<'PHP'
<?php
$host = getenv('DB_HOST') ?: 'database';
$port = getenv('DB_PORT') ?: '5432';
$database = getenv('DB_DATABASE') ?: 'renart_case_study';
$username = getenv('DB_USERNAME') ?: 'renart_user';
$password = getenv('DB_PASSWORD') ?: '';
$dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $database);

for ($attempt = 1; $attempt <= 60; $attempt++) {
    try {
        new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 2,
        ]);
        exit(0);
    } catch (Throwable $exception) {
        fwrite(STDERR, sprintf("Waiting for PostgreSQL at %s:%s (%d/60)...\n", $host, $port, $attempt));
        sleep(1);
    }
}

fwrite(STDERR, "PostgreSQL did not become available in time.\n");
exit(1);
PHP
fi

php artisan storage:link --quiet || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    php artisan db:seed --force
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
