# --- Stage 1: Builder ---
FROM php:8.4-fpm-alpine AS builder

# Dependencias para compilar extensiones y assets
RUN apk add --no-cache \
    nodejs npm \
    icu-dev \
    libzip-dev \
    libpng-dev \
    mariadb-dev \
    zlib-dev

# Extensiones necesarias para Laravel + Filament
RUN docker-php-ext-install \
    intl \
    zip \
    pcntl \
    pdo_mysql \
    bcmath \
    gd

WORKDIR /app
COPY . .

RUN npm install && npm run build

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build Vite
RUN npm install && npm run build

# --- Stage 2: Runtime (FrankenPHP) ---
FROM dunglas/frankenphp:1.4-php8.4-alpine

# Herramientas necesarias para healthcheck y entrypoint
RUN apk add --no-cache curl netcat-openbsd

# Instalar extensiones runtime
RUN install-php-extensions \
    pdo_mysql \
    intl \
    bcmath \
    gd \
    zip \
    pcntl \
    posix \
    exif \
    opcache \
    redis \
    soap

WORKDIR /app

# Copiar app ya construida
COPY --from=builder /app /app

# PHP config personalizada
COPY ./docker/php/local.ini /usr/local/etc/php/conf.d/app.ini

# Entrypoint
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Permisos Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Puerto interno
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Arranque Octane + FrankenPHP
CMD ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=80", "--admin-port=2019"]

