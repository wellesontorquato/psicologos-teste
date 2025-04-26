# Usa a imagem PHP oficial
FROM php:8.2-cli

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd bcmath

# Instala o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia o projeto para dentro do container
COPY . /var/www/html
WORKDIR /var/www/html

# Instala dependências PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Corrige permissões necessárias para o Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Gera APP_KEY se não existir
RUN if [ ! -f ".env" ]; then cp .env.example .env; fi && \
    php artisan key:generate --force

# Expondo porta 8080
EXPOSE 8080

# Comando para rodar o Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
