FROM php:8.2-fpm

WORKDIR "/application"

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl li	bpq-dev \
    && docker-php-ext-install pdo pdo_pgsql zip \

COPY . .

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Make startup script executable
RUN chmod +x render-start.sh

# Install Node.js 20
RUN apt-get install -y nodejs

RUN composer install --no-dev --optimize-autoloader

CMD ["./render-start.sh"]
