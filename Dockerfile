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
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd bcmath

# Instala o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia o código
COPY . /var/www/html
WORKDIR /var/www/html

# Dá permissão de execução ao entrypoint
RUN chmod +x /var/www/html/entrypoint.sh

# Instala dependências PHP
RUN composer install --no-interaction --prefer-dist

# Expondo porta 8080
EXPOSE 8080

# Usa o entrypoint que migra e serve
CMD ["./entrypoint.sh"]
