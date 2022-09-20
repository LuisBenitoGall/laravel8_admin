#!/bin/sh
echo "composer install"
composer install
echo "npm install"
npm install
echo "php artisan migrate"
php artisan migrate
echo "regenerate APP_KEY"
php artisan key:generate 
echo "php artisan db:seed"
php artisan db:seed
#echo "composer dumpautoload"
#composer dumpautoload