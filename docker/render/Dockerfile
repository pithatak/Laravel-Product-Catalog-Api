FROM php:8.2-fpm

# Install SQLite + PHP extensions
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libzip-dev zip unzip git curl sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite zip

# Copy composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project
COPY . .

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Create sqlite database
RUN mkdir -p database \
    && touch database/database.sqlite \
    && chmod 666 database/database.sqlite

# Fix permissions
RUN chmod -R 775 storage bootstrap/cache

# Clear configs (NOW vendor folder exists)
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan cache:clear

# Run migrations
RUN php artisan migrate --force

# Import CSV
RUN php artisan app:import-products

# Build frontend
RUN npm install
RUN npm run build

ENV PORT=10000
EXPOSE 10000

CMD php -S 0.0.0.0:$PORT -t public
