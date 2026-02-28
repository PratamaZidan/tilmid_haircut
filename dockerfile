FROM php:8.2-cli

# System libs + PHP extensions (gd wajib untuk phpspreadsheet, zip & pdo_mysql umum untuk laravel)
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install gd pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# Node 20 untuk Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get update && apt-get install -y nodejs \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . .

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install deps & build assets
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm ci
RUN npm run build

EXPOSE 8080
CMD php artisan serve --host 0.0.0.0 --port ${PORT:-8080}