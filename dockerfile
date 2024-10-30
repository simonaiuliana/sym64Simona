# Dockerfile
FROM php:8.3-cli

# Install dependencies and PHP extensions
RUN apt-get update -y && \
    apt-get install -y git unzip && \
    docker-php-ext-install pdo pdo_mysql && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /app

# Copy application files
COPY . /app

# Install PHP dependencies
RUN composer install

# Expose the application port
EXPOSE 8000

# Command to run the application using the built-in PHP server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
