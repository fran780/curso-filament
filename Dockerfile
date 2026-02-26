# ===== Stage 1: PHP dependencies (Composer) =====
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader
COPY . .

# ===== Stage 2: Frontend build (Vite) =====
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
COPY . .
RUN npm run build

# ===== Stage 3: Runtime (Nginx + PHP-FPM) =====
FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx supervisor icu-dev oniguruma-dev libzip-dev bash \
  && docker-php-ext-install pdo pdo_mysql mbstring intl zip opcache

WORKDIR /var/www/html

# App code
COPY . .

# Vendor + Vite build
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Nginx + Supervisor
RUN mkdir -p /run/nginx
COPY ./docker/nginx.conf /etc/nginx/http.d/default.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

EXPOSE 8080
CMD ["supervisord", "-c", "/etc/supervisord.conf"]