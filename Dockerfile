# STAGE 1: BUILDER (compila dependencias PHP y frontend)
FROM php:8.4-fpm-alpine AS builder

# Instalamos dependencias del sistema necesarias para:
# - Compilar extensiones PHP
# - Ejecutar npm/vite
RUN apk add --no-cache \
    nodejs npm \
    icu-dev \
    libzip-dev \
    libpng-dev \
    mysql-dev \
    zlib-dev
    
# Compilamos extensiones PHP requeridas por Laravel y la app
RUN docker-php-ext-install intl zip pcntl pdo_mysql bcmath gd

# Directorio de trabajo dentro del contenedor Dokploy
WORKDIR /app

# Copiar el proyecto
COPY . .

# Copiar el binario de Composer e Instalar dependencias PHP (producción)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalamos dependencias Node y compilar assets (Vite)
RUN  if [ -f package-lock.json ]; then npm ci; else npm install; fi \
    && npm run build

# STAGE 2: PRODUCTION (runtime con FrankenPHP + Octane)
FROM dunglas/frankenphp:1.4-php8.4-alpine

# Descargar el instalador de extensiones PHP y hacerlo ejecutable
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/install-php-extensions
RUN chmod +x /usr/local/bin/install-php-extensions

# Instalar las extensiones PHP necesarias para Laravel y la app
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

# Copiamos la aplicación ya construida desde el stage builder (incluye vendor y build de Vite)
COPY --from=builder /app /app

# Variable usada por FrankenPHP/Caddy (puerto interno) y se indica que a Laravel que use el runtime de Octane con FrankenPHP
ENV SERVER_NAME=:8081
ENV APP_RUNTIME=Laravel\Octane\FrankenPhp\Runtime

# Creamos los directorios de almacenamiento y asignar los permisos necesarios
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Este es el puerto que usara Octane dentro del contenedor
EXPOSE 8081

## Comando de arranque:
# - Detecta el binario de FrankenPHP
# - Inicia Laravel Octane usando FrankenPHP
# - Escucha en todas las interfaces en el puerto 8081
CMD ["sh", "-lc", "export FRANKENPHP_BINARY=$(command -v frankenphp || echo /usr/local/bin/frankenphp); php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8081"]