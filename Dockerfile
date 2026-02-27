FROM php:8.4-fpm-alpine AS builder

RUN apk add --no-cache \
    nodejs npm \
    icu-dev \
    libzip-dev \
    libpng-dev \
    mysql-dev \
    zlib-dev

RUN docker-php-ext-install intl zip pcntl pdo_mysql bcmath gd

WORKDIR /app
COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN  if [ -f package-lock.json ]; then npm ci; else npm install; fi \
    && npm run build

# --- Stage 2: Production ---
FROM dunglas/frankenphp:1.4-php8.4-alpine

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/install-php-extensions
RUN chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions \
    pdo_mysql \
    redis \
    intl \
    bcmath \
    gd \
    zip \
    exif \
    pcntl \
    opcache

WORKDIR /app

COPY --from=builder /app /app

ENV SERVER_NAME=:80
ENV APP_RUNTIME=Laravel\Octane\FrankenPhp\Runtime

RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 8081

CMD ["sh", "-lc", "export FRANKENPHP_BINARY=$(command -v frankenphp || echo /usr/local/bin/frankenphp); php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8081"]