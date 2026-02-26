# --- Stage 1: Builder (Composer + Vite) ---
FROM php:8.4-cli-alpine AS builder
WORKDIR /app

# System deps for building PHP extensions + node
RUN apk add --no-cache \
    git curl bash \
    nodejs npm \
    icu-dev libzip-dev zlib-dev \
    libpng-dev freetype-dev libjpeg-turbo-dev \
    oniguruma-dev

# PHP extensions often needed during build (safe set)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl zip bcmath gd

# Copy project
COPY . .

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# Vite build
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi \
    && npm run build


# --- Stage 2: Runtime (FrankenPHP + Octane) ---
FROM dunglas/frankenphp:1.4-php8.4-alpine
WORKDIR /app

# install-php-extensions (robusto)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/install-php-extensions
RUN chmod +x /usr/local/bin/install-php-extensions

# Runtime PHP extensions (MySQL + Filament common)
RUN install-php-extensions \
    pdo_mysql \
    intl \
    bcmath \
    gd \
    zip \
    exif \
    opcache \
    pcntl

# Copy built app (includes vendor + public/build)
COPY --from=builder /app /app

# Permissions for storage/cache (uploads/imports)
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 8080

CMD ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8080"]