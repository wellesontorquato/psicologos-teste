# Usa a imagem PHP oficial
FROM php:8.2-cli

# Instala dependências
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd bcmath

# Instala o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia o código
COPY . /var/www/html
WORKDIR /var/www/html

# Instala dependências PHP
RUN composer install --no-interaction --prefer-dist

# Expondo porta 8080
EXPOSE 8080

# Comando para rodar o Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
