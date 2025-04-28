# Usa imagem base PHP oficial
FROM php:8.2-fpm

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    unzip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    npm \
    && docker-php-ext-install pdo pdo_mysql zip gd bcmath

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia arquivos da aplicação
COPY . /var/www/html

# Ajusta diretório
WORKDIR /var/www/html

# Instala dependências PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Instala dependências NPM e faz build do Vite
RUN npm install && npm run build

# Copia configurações
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf

# Corrige permissões corretas apenas onde necessário
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expor porta correta
EXPOSE 8080

# Comando para iniciar nginx + php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
