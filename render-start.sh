#!/bin/bash

php artisan key:generate --force
php artisan migrate --force
php artisan app:import-products

php -S 0.0.0.0:$PORT -t public
