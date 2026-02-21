FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    sqlite3 \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first (for Docker layer caching)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copy package files and build frontend
COPY package.json package-lock.json* ./
RUN npm install && npm run build

# Copy rest of the application
COPY . /var/www

# Re-run composer for post-install scripts
RUN composer dump-autoload --optimize

# Set permissions
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Create persistent data mount point
# On Render, /var/data is mounted as a persistent disk
RUN mkdir -p /var/data/database \
    && mkdir -p /var/data/documents \
    && mkdir -p /var/data/previews

# Copy the startup script
COPY docker/start.sh /var/www/docker/start.sh
RUN chmod +x /var/www/docker/start.sh

# Render uses PORT env variable
EXPOSE 8000

CMD ["/var/www/docker/start.sh"]
