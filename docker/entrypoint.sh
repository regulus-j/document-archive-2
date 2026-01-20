#!/bin/sh

# Exit on fail
set -e

# Run standard migrations (safe)
echo "Running migrations..."
php artisan migrate --force

# Run seeders (IDEMPOTENTLY)
echo "Running seeders..."
# Critical seeders for app functionality
php artisan db:seed --class=PermissionTableSeeder --force
php artisan db:seed --class=RolesSeeder --force
php artisan db:seed --class=FeatureSeeder --force
php artisan db:seed --class=PlanSeeder --force
php artisan db:seed --class=DocumentCategories --force

echo "Database Setup Completed!"

# Clear caches
echo "Clearing caches..."
php artisan optimize:clear
php artisan view:cache
php artisan config:cache
php artisan route:cache

# Start Supervisor
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
