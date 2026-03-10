FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    nodejs \
    npm \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    oniguruma-dev \
    icu-dev \
    redis \
    autoconf \
    g++ \
    make

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        xml \
        intl \
        opcache

# Install phpredis extension
RUN pecl install redis \
    && docker-php-ext-enable redis \
    && apk del autoconf g++ make

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (layer caching)
COPY composer.json composer.lock ./

# Install PHP dependencies (no dev)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy package.json for npm
COPY package.json package-lock.json ./

# Install Node dependencies
RUN npm ci

# Copy the rest of the application
COPY . .

# Generate optimized autoloader & run post-install scripts
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi

# Build Vite assets
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy config files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]