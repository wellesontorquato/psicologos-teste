FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    unzip \
    curl \
    git \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip mbstring gd bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

WORKDIR /var/www/html

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
