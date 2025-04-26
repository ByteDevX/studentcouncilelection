# syntax=docker/dockerfile:1

########################
# 1. Vendors + GD      #
########################
FROM php:8.3-cli-alpine AS vendor

# ---- system libs needed for GD ----
RUN apk add --no-cache \
      libpng-dev libjpeg-turbo-dev freetype-dev libwebp-dev \
      icu-dev libzip-dev zlib-dev git curl

# ---- PHP extensions ----
RUN docker-php-ext-configure gd \
        --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j$(nproc) gd intl zip opcache

# ---- Composer (already included in official php images) ----
ENV COMPOSER_HOME=/tmp/composer
COPY --link --from=composer:2.8 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

########################
# 2. Front-end assets  #
########################
FROM node:20 AS frontend
WORKDIR /app

# 1️⃣ install deps
COPY package.json package-lock.json ./
RUN npm ci --quiet

# 2️⃣ copy ONLY what the asset build needs
COPY vite.config.js .                      # vite config
COPY resources resources                   # source JS/CSS
COPY public/ public/                      # if you keep assets here
# (optional) COPY resources/views resources/views  # if your config uses Blade as input

RUN npm run build


########################
# 3. Runtime image     #
########################
FROM php:8.3-fpm-alpine AS runtime

RUN apk add --no-cache \
      libpng libjpeg-turbo freetype libwebp icu libzip zlib \
      libpng-dev libjpeg-turbo-dev freetype-dev libwebp-dev
RUN docker-php-ext-configure gd \
        --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j$(nproc) gd intl zip pdo pdo_mysql opcache

WORKDIR /var/www
COPY --from=vendor   /app            ./
COPY --from=frontend /app/public/build public/build
RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data
EXPOSE 9000
CMD ["php-fpm"]
