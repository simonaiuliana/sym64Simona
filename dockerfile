# Use PHP 8.1 with FPM on Alpine 
FROM php:8.1-fpm-alpine3.16

# Install necessary packages without cache
RUN apk add --no-cache bash git libzip-dev libpng-dev libjpeg-turbo-dev libwebp-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install gd zip

# Set the working directory
WORKDIR /usr/src/app

# Copy Composer configuration files
COPY composer.json composer.lock ./

# Add vendor/bin to PATH
ENV PATH="$PATH:/usr/src/app/vendor/bin"

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader