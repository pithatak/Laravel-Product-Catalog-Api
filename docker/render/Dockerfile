FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

RUN mkdir -p database \
    && touch database/database.sqlite \
    && chmod 666 database/database.sqlite

RUN chmod -R 775 storage bootstrap/cache

RUN php artisan config:clear
RUN php artisan route:clear

ENV PORT=10000
EXPOSE 10000

CMD php -S 0.0.0.0:$PORT -t public
