#!/usr/bin/env sh
set -e

echo "=== Booting Movie Ticket API on Render ==="

# Default PORT to 10000 if not provided by Render
export PORT=${PORT:-10000}
echo "Configuring Nginx to listen on port ${PORT}..."

# Substitute $PORT into Nginx config
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

# Cache configuration, routes, and views for maximum production performance
echo "Caching Laravel configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Execute migrations in production
echo "Running database migrations on NeonDB..."
php artisan migrate --force

# Seed database if RUN_SEEDERS is set to true
if [ "$RUN_SEEDERS" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force
fi

# Ensure storage link exists
php artisan storage:link --quiet || true

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
php-fpm -D

# Start Nginx in foreground
echo "Starting Nginx web server on port ${PORT}..."
exec nginx -g "daemon off;"

