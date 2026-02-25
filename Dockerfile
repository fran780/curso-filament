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

# ===== Stage 3: Runtime (PHP-FPM + Nginx) =====
FROM php:8.4-fpm-alpine

RUN apk add --no-cache nginx supervisor icu-dev oniguruma-dev libzip-dev \
  && docker-php-ext-install pdo pdo_mysql mbstring intl zip opcache

WORKDIR /var/www/html

# App code
COPY . .

# Vendor
COPY --from=vendor /app/vendor ./vendor

# Vite build output
COPY --from=assets /app/public/build ./public/build

# Nginx + Supervisor configs
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Laravel permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]
[1:59 p.m., 25/2/2026] Francisco Fernández: server {
  listen 80;
  server_name _;
  root /var/www/html/public;
  index index.php;

  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }

  location ~ \.php$ {
    try_files $uri =404;
    include fastcgi_params;
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
  }
}