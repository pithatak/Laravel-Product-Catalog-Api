FROM php:8.2-fpm

WORKDIR "/application"

RUN apt-get update && apt-get install -y php8.2-mysql curl git unzip

# Install Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

COPY . .

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Make startup script executable
RUN chmod +x render-start.sh

RUN composer install --no-dev --optimize-autoloader

CMD ["./render-start.sh"]
