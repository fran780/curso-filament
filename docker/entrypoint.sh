#!/usr/bin/env sh
set -e

echo "==> Starting entrypoint..."

# Ensure APP_KEY exists
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
  echo "==> APP_KEY missing. Generating..."
  php artisan key:generate --force || true
fi

# Wait for DB (MySQL)
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"

echo "==> Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
for i in $(seq 1 60); do
  nc -z "$DB_HOST" "$DB_PORT" && break
  echo "   ... still waiting ($i/60)"
  sleep 1
done

# Storage link (safe)
echo "==> storage:link"
php artisan storage:link || true

# Migrations
echo "==> migrate --force"
php artisan migrate --force || true

# Caches / optimize
echo "==> optimize"
php artisan optimize || true

# Filament optimize (optional)
echo "==> filament:optimize (optional)"
php artisan filament:optimize || true

echo "==> Handing off to CMD: $*"
exec "$@"