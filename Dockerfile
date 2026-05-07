FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    zip unzip libzip-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www/html

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

RUN cp .env.example .env && \
    php artisan key:generate

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]