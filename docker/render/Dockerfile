FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Create sqlite database file
RUN mkdir -p database \
    && touch database/database.sqlite \
    && chmod 666 database/database.sqlite

# Fix permissions
RUN chmod -R 775 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader

# Run migrations
RUN php artisan migrate --force

# Import CSV
RUN php artisan app:import-products

ENV PORT=10000
EXPOSE 10000

CMD php -S 0.0.0.0:$PORT -t public
