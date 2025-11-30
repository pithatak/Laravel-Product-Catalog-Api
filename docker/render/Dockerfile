FROM php:8.2-fpm

# Install system deps + sqlite dev headers
RUN apt-get update && apt-get install -y \
    libsqlite3-dev libzip-dev zip unzip git curl \
    && docker-php-ext-install pdo pdo_sqlite zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# Create sqlite file
RUN mkdir -p database \
    && touch database/database.sqlite \
    && chmod 666 database/database.sqlite

# Create .env from example and force DB -> sqlite for build-time migrations
RUN if [ -f .env.example ]; then cp .env.example .env; else touch .env; fi \
    && sed -i -E "s/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/" .env \
    && sed -i -E "s|^DB_DATABASE=.*|DB_DATABASE=/var/www/html/database/database.sqlite|" .env \
    && sed -i -E "s/^APP_ENV=.*/APP_ENV=production/" .env || true

# Generate app key (requires vendor)
RUN php artisan key:generate --force

# Fix permissions
RUN chmod -R 775 storage bootstrap/cache

# Clear caches just in case
RUN php artisan config:clear || true
RUN php artisan route:clear || true
RUN php artisan cache:clear || true

# Run migrations & import CSV
RUN php artisan migrate --force
RUN php artisan app:import-products

# Build frontend
RUN apt-get install -y curl gnupg # if not present
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

ENV PORT=10000
EXPOSE 10000

CMD php -S 0.0.0.0:$PORT -t public
