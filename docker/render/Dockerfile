FROM node:20 AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY . .
RUN npm run build


FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    && docker-php-ext-install pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=frontend /app/public/build public/build

RUN composer install --no-dev --optimize-autoloader

RUN php artisan key:generate --force || true

RUN php artisan config:cache || true
RUN php artisan route:cache || true

ENV PORT=10000
EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
