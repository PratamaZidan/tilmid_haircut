# --- Stage 1: Node (build Vite assets) ---
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci

COPY . .
RUN npm run build

# --- Stage 2: PHP (final image) ---
FROM php:8.2-fpm-alpine AS php-base

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libxml2-dev \
    oniguruma-dev \
    libzip-dev \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    xml \
    curl \
    zip \
    gd \
    bcmath \
    tokenizer \
    ctype \
    fileinfo \
    opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files first (layer caching)
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Copy rest of app
COPY . .

# Copy built Vite assets from node stage
COPY --from=node-builder /app/public/build ./public/build

# Run composer scripts after full copy
RUN composer run-script post-autoload-dump || true

# Set permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage \
    && chmod -R 755 /app/bootstrap/cache

# --- Nginx config ---
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# --- Supervisor config (manage nginx + php-fpm) ---
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# --- PHP opcache config ---
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

EXPOSE 8000

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

CMD ["/entrypoint.sh"]