FROM php:8.2-cli

# Use the community PHP extension installer (handles all deps automatically)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

# Install system deps
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip sqlite3 \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (the installer resolves all dependencies)
RUN install-php-extensions pdo_sqlite mbstring bcmath zip

# Install Node.js 20 for Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files first (layer caching)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copy package files and build frontend
COPY package.json package-lock.json* ./
RUN npm ci && npm run build

# Copy rest of the application
COPY . /var/www
RUN composer dump-autoload --optimize

# Permissions and directories
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && mkdir -p /var/data/database /var/data/documents /var/data/previews

# Startup script
COPY docker/start.sh /var/www/docker/start.sh
RUN chmod +x /var/www/docker/start.sh

EXPOSE 8000
CMD ["/var/www/docker/start.sh"]
