#!/bin/bash
set -e

# Set proper permissions for storage and cache directories
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Execute the main command
exec "$@"
