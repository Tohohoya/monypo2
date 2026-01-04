#!/bin/bash

# Laravel キャッシュを使わない
php artisan config:clear
php artisan migrate --force

# サーバー起動
php -S 0.0.0.0:$PORT -t public