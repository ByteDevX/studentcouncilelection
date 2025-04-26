# Use an official PHP image with Apache
FROM php:8.4-apache

# Install required PHP extensions and dependencies for Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Install Node.js and npm
RUN curl -sL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the Laravel app files into the container
COPY . /var/www/html

# Install Composer and Laravel dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Install NPM dependencies and build assets
RUN npm install \
    && npm run prod

# Set the correct file permissions for Laravel's storage and bootstrap/cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80 for the Apache server
EXPOSE 80

# Set the default command to run Apache in the foreground
CMD ["apache2-foreground"]
