FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    && rm -rf /var/lib/apt/lists/*

# Install only the PHP extensions this app actually needs
RUN docker-php-ext-install pdo pdo_sqlite mbstring bcmath zip

# Install Node.js 20 for Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first (layer caching)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copy package files and build frontend
COPY package.json package-lock.json* ./
RUN npm ci && npm run build

# Copy rest of the application
COPY . /var/www

# Re-run composer for post-install scripts
RUN composer dump-autoload --optimize

# Set permissions
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Create persistent data directories
RUN mkdir -p /var/data/database /var/data/documents /var/data/previews

# Startup script
COPY docker/start.sh /var/www/docker/start.sh
RUN chmod +x /var/www/docker/start.sh

EXPOSE 8000
CMD ["/var/www/docker/start.sh"]
