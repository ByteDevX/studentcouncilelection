# syntax=docker/dockerfile:1

########################
# 1. Vendors (Composer) #
########################
FROM composer:2.8 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

########################
# 2. Front-end assets  #
########################
FROM node:20 AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --quiet
COPY resources resources
RUN npm run build      # vite → /app/public/build

########################
# 3. Runtime image     #
########################
FROM php:8.3-fpm-alpine AS runtime

RUN apk add --no-cache icu-dev libzip-dev zlib-dev git curl \
 && docker-php-ext-install pdo pdo_mysql intl zip opcache

WORKDIR /var/www
COPY --from=vendor   /app            ./
COPY --from=frontend /app/public/build public/build
RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data
EXPOSE 9000
CMD ["php-fpm"]
