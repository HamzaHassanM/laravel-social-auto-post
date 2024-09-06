# Use the official PHP image with CLI and necessary extensions
FROM php:8.2-cli

# Set working directory
WORKDIR /app

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy the application code
COPY . .

# Install PHP dependencies
RUN composer install

# Command to run tests
CMD ["vendor/bin/phpunit"]
