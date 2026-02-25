# 1) Build de assets (Vite) - soporta con o sin package-lock.json
FROM node:20-alpine AS assets
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
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install intl gd zip pdo_mysql \
  && rm -rf /var/lib/apt/lists/*

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader


# 3) Runtime (FrankenPHP) + mismas extensiones
FROM dunglas/frankenphp:latest
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

# 1GB RAM: pocos workers
CMD ["php","artisan","octane:frankenphp","--host=0.0.0.0","--port=8000","--workers=2","--max-requests=300"]