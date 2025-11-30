#!/bin/bash

php artisan key:generate --force
php artisan migrate --force
php artisan app:import-products

RUN npm install
RUN npm run build

php -S 0.0.0.0:$PORT -t public
