#!/bin/sh
set -e

# Wait for database to be ready
echo "Waiting for database..."
if [ -n "$DB_HOST" ]; then
  while ! nc -z "$DB_HOST" "${DB_PORT:-5432}"; do
    sleep 1
  done
  echo "Database is ready!"
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Cache configuration
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start services
echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
