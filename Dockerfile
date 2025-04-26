# Atualizado
FROM php:8.2-fpm

# Instalar dependências
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

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar aplicação
COPY . /var/www/html
WORKDIR /var/www/html

# Criar .env a partir do .env.example
RUN cp .env.example .env

# Instalar dependências PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Gerar chave do Laravel
RUN php artisan key:generate

# Corrigir permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expor porta
EXPOSE 80

# Rodar Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
