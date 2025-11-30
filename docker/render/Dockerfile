FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Make startup script executable
RUN chmod +x render-start.sh

RUN composer install --no-dev --optimize-autoloader

# Build frontend
RUN apt-get install -y nodejs

ENV PORT=10000
EXPOSE 10000

CMD ["./render-start.sh"]
