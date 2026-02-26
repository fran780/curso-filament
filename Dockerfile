# --- Stage 1: Builder (Composer + Vite) ---
FROM php:8.4-cli-alpine AS builder

# System deps for building PHP extensions + node
RUN apk add --no-cache \
    git curl bash \
    nodejs npm \
    icu-dev libzip-dev zlib-dev \
    libpng-dev freetype-dev libjpeg-turbo-dev \
    oniguruma-dev

# PHP extensions needed during build
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl zip bcmath gd

WORKDIR /app
COPY . .

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# Vite build
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi \
    && npm run build


# --- Stage 2: Runtime (FrankenPHP + Octane) ---
FROM dunglas/frankenphp:1.5-php8.4-alpine

WORKDIR /app

# PHP extension installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/install-php-extensions
RUN chmod +x /usr/local/bin/install-php-extensions

# Runtime extensions
RUN install-php-extensions \
    pdo_mysql \
    redis \
    intl \
    bcmath \
    gd \
    zip \
    pcntl \
    posix \
    exif \
    opcache \
    soap

# Copy built app
COPY --from=builder /app /app

# Permissions
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Puerto que usará Traefik
EXPOSE 9000