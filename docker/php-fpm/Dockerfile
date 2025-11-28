FROM phpdockerio/php:8.2-fpm

WORKDIR "/application"

RUN apt-get update; \
    apt-get -y --no-install-recommends install \
        php8.2-mysql; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY . .

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

CMD ["php-fpm8.2"]
