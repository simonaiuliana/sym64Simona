FROM php:8.2-fpm

# Instalare dependințe
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev

# Instalare extensii PHP
RUN docker-php-ext-install \
    pdo_mysql \
    zip \
    intl

# Instalare Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Setare permisiuni
RUN chown -R www-data:www-data /var/www/html 