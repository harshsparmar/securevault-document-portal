#!/bin/bash
set -e

# ============================================
# SecureVault — Render Startup Script
# ============================================

# Ensure persistent directories exist
mkdir -p /var/data/database
mkdir -p /var/data/documents
mkdir -p /var/data/previews

# Create SQLite database if it doesn't exist
if [ ! -f /var/data/database/database.sqlite ]; then
    touch /var/data/database/database.sqlite
    echo "Created new SQLite database"
fi

# Create symlinks from app storage to persistent disk
ln -sf /var/data/documents /var/www/storage/app/private/documents
ln -sf /var/data/previews /var/www/storage/app/private/previews

# Ensure storage directories exist
mkdir -p /var/www/storage/app/private
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/logs

# Set permissions
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Run migrations
php artisan migrate --force

# Cache config and routes for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start the application
# Use PORT env var from Render, default to 8000
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
