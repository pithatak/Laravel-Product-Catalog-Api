FROM php:8.2-fpm

# Install system deps + sqlite dev headers
RUN apt-get update && apt-get install -y \
    libsqlite3-dev libzip-dev zip unzip git curl sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# ---- FORCE Laravel to use SQLite ALWAYS during build ----
RUN sed -i "s/'mysql' => \[/'mysql_DISABLED' => \[/g" config/database.php \
    && sed -i "s/'default' => env('DB_CONNECTION', 'mysql')/'default' => 'sqlite'/g" config/database.php

# Create sqlite file
RUN mkdir -p database \
    && touch database/database.sqlite \
    && chmod 666 database/database.sqlite

# Install deps
RUN composer install --no-dev --optimize-autoloader

# Remove config cache if exists
RUN rm -f bootstrap/cache/config.php

# Generate key
RUN php artisan key:generate --force

# Migrate with SQLite
RUN php artisan migrate --force

# Import CSV
RUN php artisan app:import-products

# Node build
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# Fix permissions
RUN chmod -R 775 storage bootstrap/cache

ENV PORT=10000
EXPOSE 10000

CMD php -S 0.0.0.0:$PORT -t public
