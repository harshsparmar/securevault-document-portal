#!/bin/bash
set -e

echo "=== SecureVault Startup ==="

# Ensure persistent directories exist
mkdir -p /var/data/database
mkdir -p /var/data/documents
mkdir -p /var/data/previews

# Create SQLite database if it doesn't exist
NEED_SEED=false
if [ ! -f /var/data/database/database.sqlite ]; then
    touch /var/data/database/database.sqlite
    NEED_SEED=true
    echo "Created new SQLite database"
fi

# Create symlinks from app storage to persistent disk
mkdir -p /var/www/storage/app/private
ln -sf /var/data/documents /var/www/storage/app/private/documents
ln -sf /var/data/previews /var/www/storage/app/private/previews

# Ensure storage directories exist
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/framework/cache/data
mkdir -p /var/www/storage/logs

# Set permissions
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY not set, generating..."
    php artisan key:generate --force
fi

# Set timezone correctly in the .env if not already set by Render vars
if ! grep -q "^APP_TIMEZONE=" /var/www/.env; then
    echo "APP_TIMEZONE=${APP_TIMEZONE:-Asia/Kolkata}" >> /var/www/.env
fi

# Clear any cached config (important: env vars from Render override .env)
php artisan config:clear

# Run migrations
php artisan migrate --force
echo "Migrations complete"

# Seed on first run only (when database was just created)
if [ "$NEED_SEED" = true ]; then
    php artisan db:seed --force
    echo "Database seeded with demo accounts"
fi

# Cache routes and views for performance
php artisan route:cache
php artisan view:cache
echo "Caches built"

echo "=== Starting server on port ${PORT:-8000} ==="
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
