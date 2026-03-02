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

# Route cache — may fail if duplicate route names exist; non-fatal
echo "Attempting route cache..."
if php artisan route:cache 2>&1; then
    echo "Route cache created."
else
    echo "WARNING: route:cache failed (likely duplicate route names). Skipping — app will work without it."
fi

# Verify external Ollama connectivity
echo "Checking external Ollama connection..."
OLLAMA_URL="${OLLAMA_BASE_URL:-http://localhost:11434}"
RETRIES=0
MAX_RETRIES=3
OLLAMA_OK=false
while [ "$RETRIES" -lt "$MAX_RETRIES" ]; do
    if curl -sf "${OLLAMA_URL}/api/tags" > /dev/null 2>&1; then
        OLLAMA_OK=true
        break
    fi
    RETRIES=$((RETRIES + 1))
    echo "  Attempt ${RETRIES}/${MAX_RETRIES} — Ollama not yet reachable..."
    sleep 5
done

if [ "$OLLAMA_OK" = true ]; then
    echo "Ollama is reachable at ${OLLAMA_URL}"
else
    echo "WARNING: Ollama is not reachable at ${OLLAMA_URL}."
    echo "  Ensure the Ollama droplet is running and OLLAMA_BASE_URL is set correctly."
    echo "  Summarization / AI features will fall back to text excerpts."
fi

# Start Supervisor
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
