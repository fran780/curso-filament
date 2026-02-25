# 1) Build de assets (Vite) - soporta con o sin package-lock.json
FROM php:8.4-fpm-alpine AS builder
WORKDIR /app

COPY package.json ./
COPY package-lock.json* ./

RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

COPY . .
RUN npm run build


# 2) Composer deps (con extensiones requeridas por Filament: intl, gd, etc.)
FROM composer:2 AS vendor
WORKDIR /app

# Instalar libs necesarias para intl/gd/zip
RUN apk add --no-cache \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    unzip \
    oniguruma-dev \
    linux-headers \
    $PHPIZE_DEPS \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install intl gd zip pdo_mysql

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader


# 3) Runtime (FrankenPHP) + mismas extensiones
FROM dunglas/frankenphp:1.4-php8.4-alpine
WORKDIR /app

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install intl gd zip pdo_mysql \
  && rm -rf /var/lib/apt/lists/*

COPY . .
COPY --from=vendor /app/vendor /app/vendor
COPY --from=assets /app/public/build /app/public/build

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 8000

# Default command starts FrankenPHP in high-performance Worker Mode
CMD ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8000", "--admin-port=2019"]