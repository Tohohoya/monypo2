#!/bin/bash
php artisan config:cache
php artisan migrate --force
php -S 0.0.0.0:$PORT -t public

# .env を生成
echo "APP_KEY=$(php artisan key:generate --show)" > .env
echo "APP_URL=https://monypo2-production.up.railway.app" >> .env
echo "DB_CONNECTION=${DB_CONNECTION}" >> .env
echo "DB_HOST=${DB_HOST}" >> .env
echo "DB_PORT=${DB_PORT}" >> .env
echo "DB_DATABASE=${DB_DATABASE}" >> .env
echo "DB_USERNAME=${DB_USERNAME}" >> .env
echo "DB_PASSWORD=${DB_PASSWORD}" >> .env

# Laravel キャッシュとマイグレーション
php artisan config:cache
php artisan migrate --force

# サーバー起動
php -S 0.0.0.0:$PORT -t public