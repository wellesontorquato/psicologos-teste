# Stage 1: Build Frontend
FROM node:18 as nodebuilder

WORKDIR /app

COPY package*.json vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ resources/

RUN npm install
RUN npm run build

# Stage 2: Build Backend + PHP
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    unzip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libpq-dev \
    libmcrypt-dev \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql zip gd bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copia app completo
COPY . .

# Copia build gerado pelo Node
COPY --from=nodebuilder /app/public/build /var/www/html/public/build

# Instala dependências PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permissões
RUN chmod -R 777 storage bootstrap/cache

# Copia nginx.conf e supervisord.conf
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf

# Expondo a porta dinamicamente
EXPOSE 8080

# Comando para rodar supervisord que gerencia nginx e php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
