FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev libxml2-dev \
    gnupg

# Install Node.js versi 22
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel project files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Install JS dependencies and build assets
RUN npm install && npm run build

# Generate application key
RUN php artisan key:generate

# Fresh migrate database and seed
RUN php artisan migrate:fresh --seed || true
# pakai `|| true` biar kalau database belum ready saat build, tidak error. Migrasi ulang di CMD.

# Set permissions
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Expose port
EXPOSE 50002

# Start server + database migrate + seeder di runtime
CMD php artisan serve --host=0.0.0.0 --port=50002
