#!/bin/bash

# .env を生成
cat <<EOF > .env
APP_KEY=$(php artisan key:generate --show)
APP_URL=https://monypo2-production.up.railway.app
DB_CONNECTION=mysql
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD
EOF

# Laravel キャッシュとマイグレーション
php artisan config:clear
php artisan config:cache
php artisan migrate --force

# サーバー起動
php -S 0.0.0.0:$PORT -t public