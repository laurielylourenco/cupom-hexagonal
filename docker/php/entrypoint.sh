#!/usr/bin/env bash
set -e

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -qE '^APP_KEY=.+' .env; then
    php artisan key:generate --force
fi

until pg_isready -h "${DB_HOST:-db}" -p "${DB_PORT:-5432}" -U "${DB_USERNAME:-cupom}" >/dev/null 2>&1; do
    echo "Aguardando PostgreSQL em ${DB_HOST:-db}:${DB_PORT:-5432}..."
    sleep 1
done

php artisan migrate --force

exec "$@"
