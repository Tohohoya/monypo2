FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    zip unzip git libpq-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql

COPY . /var/www
WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

CMD php artisan migrate --force && php -S 0.0.0.0:$PORT -t public
