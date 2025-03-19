FROM php:8.2-apache

# Install required dependencies including MySQLi
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/

# Copy application files first
COPY ./php-mail-app/config/ /var/www/config/
COPY ./php-mail-app/src/ /var/www/html/

# Ensure composer.json exists before installing dependencies
RUN composer require guzzlehttp/guzzle:^7.0 --no-interaction --prefer-dist

# Expose port
EXPOSE 80
