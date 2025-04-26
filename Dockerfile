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
RUN composer install --no-dev --prefer-dist --no-interaction \
        --optimize-autoloader --no-scripts
        
########################
# 2. Front-end assets  #
########################
FROM node:20 AS frontend
WORKDIR /app

# 1️⃣ install deps
COPY package.json package-lock.json ./
RUN npm ci --quiet

# 2️⃣ copy ONLY what the asset build needs
COPY vite.config.js . 
COPY resources resources
COPY public/ public/
# (optional) COPY resources/views resources/views  # if your config uses Blade as input

RUN npm run build


########################
# 3. Runtime image     #
########################
FROM php:8.3-fpm-alpine AS runtime

# ---------------------------- build-time deps -----------------------------
RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev icu-data-full \
        libzip-dev \
        libpng-dev libjpeg-turbo-dev freetype-dev libwebp-dev && \
# ---------------------------- compile extensions --------------------------
    docker-php-ext-configure gd \
        --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install -j"$(nproc)" gd intl zip pdo pdo_mysql opcache && \
# ---------------------------- strip build deps ----------------------------
    apk del .build-deps

# ----- runtime libs only (tiny image) -----
RUN apk add --no-cache \
        icu libzip \
        libpng libjpeg-turbo freetype libwebp

COPY --from=composer:2.8 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www
COPY --from=vendor /app/vendor vendor
COPY --from=frontend /app/public/build public/build
COPY . .
RUN composer run-script post-autoload-dump --no-dev --no-interaction
RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data
EXPOSE 9000
CMD ["php-fpm"]
