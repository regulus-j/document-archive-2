# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm ci

# Copy source files needed for build
COPY resources ./resources
COPY vite.config.js postcss.config.js tailwind.config.js ./

# Build assets
RUN npm run build

# Stage 2: PHP Application
FROM php:8.2-fpm-alpine AS production

# Ensure php is in PATH for terminal access
ENV PATH="/usr/local/bin:/usr/local/sbin:$PATH"

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    tesseract-ocr \
    tesseract-ocr-data-eng \
    poppler-utils \
    ghostscript

# Configure PHP upload limits and memory
RUN echo 'upload_max_filesize = 50M' > /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'post_max_size = 60M' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'memory_limit = 256M' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'max_execution_time = 120' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'max_input_time = 120' >> /usr/local/etc/php/conf.d/uploads.ini

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        gd \
        zip \
        bcmath \
        intl \
        mbstring \
        exif \
        pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Copy built frontend assets from stage 1
COPY --from=frontend /app/public/build ./public/build

# Generate autoloader and run Laravel optimizations
# Note: route:cache skipped due to duplicate route name - fix routes then re-enable
# Note: config:cache skipped to avoid baking in build-time env vars
RUN composer dump-autoload --optimize \
    && php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && mkdir -p /var/log/supervisor

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Copy Supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port
EXPOSE 80

# Start via entrypoint (runs migrations, seeds, then supervisor)
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
