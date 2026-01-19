# Laravel Document Archive Application
# Multi-stage build for optimized production image

# ==============================================================================
# Stage 1: Build frontend assets
# ==============================================================================
FROM node:20-alpine AS frontend-builder

WORKDIR /app

# Copy package files
COPY package.json package-lock.json ./

# Install dependencies
RUN npm ci

# Copy frontend source files
COPY resources ./resources
COPY vite.config.js postcss.config.js tailwind.config.js ./

# Build assets
RUN npm run build

# ==============================================================================
# Stage 2: Install PHP dependencies
# ==============================================================================
FROM composer:2 AS composer-builder

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies (no dev for production)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

# Copy application source
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# ==============================================================================
# Stage 3: Production image
# ==============================================================================
FROM php:8.2-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    # Required for Laravel
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    git \
    # Required for GD extension (images)
    freetype \
    freetype-dev \
    libjpeg-turbo \
    libjpeg-turbo-dev \
    libpng \
    libpng-dev \
    libwebp \
    libwebp-dev \
    # Required for ZIP extension
    libzip-dev \
    # Required for intl extension
    icu-dev \
    # Required for PDF generation (DOMPDF)
    fontconfig \
    ttf-freefont \
    # Required for Tesseract OCR
    tesseract-ocr \
    tesseract-ocr-data-eng \
    # Required for poppler (pdf-to-text)
    poppler-utils \
    # Required for MySQL
    mysql-client \
    # Required for XML processing (PHPWord/PHPSpreadsheet)
    libxml2-dev \
    # Oniguruma for mbstring
    oniguruma-dev

# Install PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        mysqli \
        zip \
        intl \
        bcmath \
        opcache \
        pcntl \
        exif \
        xml \
        dom \
        mbstring

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Configure PHP for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# PHP custom configuration
COPY <<EOF $PHP_INI_DIR/conf.d/99-laravel.ini
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 120
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 0
opcache.save_comments = 1
EOF

# Create application directory
WORKDIR /var/www/html

# Copy application from builder stages
COPY --from=composer-builder /app/vendor ./vendor
COPY --from=frontend-builder /app/public/build ./public/build

# Copy application source
COPY . .

# Remove development files
RUN rm -rf \
    node_modules \
    tests \
    .git \
    .gitignore \
    .gitattributes \
    .editorconfig \
    phpunit.xml \
    *.md \
    *.txt

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create storage link placeholder (will be created at runtime)
RUN rm -rf public/storage

# Nginx configuration
COPY <<EOF /etc/nginx/http.d/default.conf
server {
    listen 80;
    listen [::]:80;
    server_name _;
    root /var/www/html/public;
    
    index index.php index.html;
    
    charset utf-8;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Max upload size
    client_max_body_size 64M;
    
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Supervisor configuration
COPY <<EOF /etc/supervisor/conf.d/supervisord.conf
[supervisord]
nodaemon=true
user=root
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm -F
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true
priority=5

[program:nginx]
command=nginx -g 'daemon off;'
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true
priority=10

[program:laravel-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/queue.log
stopwaitsecs=3600
EOF

# Create supervisor log directory
RUN mkdir -p /var/log/supervisor

# Startup script
COPY <<EOF /usr/local/bin/docker-entrypoint.sh
#!/bin/sh
set -e

# Wait for database if DB_HOST is set
if [ -n "\$DB_HOST" ]; then
    echo "Waiting for database connection..."
    while ! nc -z \$DB_HOST \${DB_PORT:-3306}; do
        sleep 1
    done
    echo "Database is ready!"
fi

# Create storage link
php artisan storage:link --force 2>/dev/null || true

# Clear and cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations if AUTO_MIGRATE is set
if [ "\$AUTO_MIGRATE" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
fi

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
EOF

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Install netcat for health checks
RUN apk add --no-cache netcat-openbsd

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
