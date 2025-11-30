FROM php:8.2-fpm

WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libzip-dev zip unzip git curl sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .
RUN composer install --no-dev --optimize-autoloader

# Create sqlite database
RUN mkdir -p database \
    && touch database/database.sqlite \
    && chmod 666 database/database.sqlite

# Run migrations
RUN php artisan migrate --force

# Import CSV
RUN php artisan app:import-products

# Permissions
RUN chmod -R 775 storage bootstrap/cache

# Build frontend
RUN npm install
RUN npm run build

ENV PORT=10000
EXPOSE 10000

CMD php -S 0.0.0.0:$PORT -t public
