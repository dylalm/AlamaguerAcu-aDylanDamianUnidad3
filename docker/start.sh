#!/bin/sh
set -e

# El proveedor de hosting asigna el puerto en la variable PORT
PORT="${PORT:-8080}"

# Base de datos SQLite para la demostración (en producción real: PostgreSQL)
mkdir -p database && touch database/database.sqlite
chmod -R ug+rwx storage bootstrap/cache database

php artisan config:cache
php artisan migrate --force

# Datos de ejemplo para la demostración (SEED_DEMO=true)
if [ "${SEED_DEMO:-false}" = "true" ]; then
  php artisan db:seed --force
fi

# Atiende varias peticiones a la vez
export PHP_CLI_SERVER_WORKERS="${PHP_CLI_SERVER_WORKERS:-4}"

exec php artisan serve --host=0.0.0.0 --port="$PORT"