# Usa a imagem PHP oficial
FROM php:8.2-cli

# Instala dependências de sistema e PHP
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql zip gd bcmath

# Instala o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia o código da aplicação
COPY . /var/www/html
WORKDIR /var/www/html

# Instala dependências do Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expondo porta 8080
EXPOSE 8080

# Comando para iniciar o servidor Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
