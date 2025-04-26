# Use an official PHP image with Apache
FROM php:8.4-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y sudo \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    curl \
    php-cli \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Install Composer
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-installer.php && \
    php /tmp/composer-installer.php --install-dir=/usr/local/bin --filename=composer && \
    chmod +x /usr/local/bin/composer

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the Laravel app files into the container
COPY . /var/www/html

# Install Laravel dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# Install NPM dependencies and build assets
RUN npm install \
    && npm run prod

# Set the correct file permissions for Laravel's storage and bootstrap/cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80 for the Apache server
EXPOSE 80

# Set the default command to run Apache in the foreground
CMD ["apache2-foreground"]
