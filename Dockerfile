# Usa imagem base do PHP com extensões necessárias
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
    libpq-dev \ # <- para conectar no Postgres!
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd bcmath

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia arquivos da aplicação
COPY . .

# Instala dependências do PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expondo porta 8080
EXPOSE 8080

# Usa o entrypoint que migra e serve
CMD ["./entrypoint.sh"]
