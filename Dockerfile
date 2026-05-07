FROM php:8.2-apache-bookworm

# Install dependency minimal
RUN apt-get update && apt-get install -y \
    zip unzip libzip-dev libpng-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Enable apache rewrite
RUN a2enmod rewrite

# Copy project
COPY . /var/www/html
WORKDIR /var/www/html

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Laravel dependency (tanpa dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Setup Laravel
RUN cp .env.example .env
RUN php artisan key:generate

# Permission
RUN chmod -R 777 storage bootstrap/cache

# Set public folder
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80