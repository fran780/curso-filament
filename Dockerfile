FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
COPY pnpm-lock.yaml ./
RUN corepack enable || true
RUN if [ -f pnpm-lock.yaml ]; then pnpm i --frozen-lockfile; else npm ci; fi
COPY . .
RUN if [ -f pnpm-lock.yaml ]; then pnpm build; else npm run build; fi

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

FROM dunglas/frankenphp:latest
WORKDIR /app
RUN apt-get update && apt-get install -y unzip git && rm -rf /var/lib/apt/lists/*

COPY . .
COPY --from=vendor /app/vendor /app/vendor
COPY --from=assets /app/public/build /app/public/build

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 8000

CMD ["php","artisan","octane:frankenphp","--host=0.0.0.0","--port=8000","--workers=2","--max-requests=300"]