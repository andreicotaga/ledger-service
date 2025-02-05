FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Symfony project files
COPY . .

# Install dependencies
RUN composer install --no-interaction --prefer-dist

# Set correct permissions
RUN chmod -R 777 var/

# Expose port 9000 (PHP-FPM)
EXPOSE 9000

CMD ["php-fpm"]
