#!/usr/bin/env sh
set -e

echo "Starting Movie Ticket API entrypoint..."

# Wait for PostgreSQL to become available
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database connection at $DB_HOST:${DB_PORT:-5432}..."
    until nc -z -v -w30 "$DB_HOST" "${DB_PORT:-5432}"; do
        echo "Waiting for PostgreSQL to be ready..."
        sleep 2
    done
    echo "PostgreSQL is reachable!"
fi

# Ensure composer dependencies are installed
if [ ! -d "/var/www/vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist
fi

# Ensure application key is generated
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed database if requested
if [ "$RUN_SEEDERS" = "true" ]; then
    echo "Seeding database with demo data..."
    php artisan db:seed --force
fi

# Create storage symlink if not already created
php artisan storage:link --quiet || true

echo "Application is ready. Starting PHP-FPM..."
exec php-fpm

