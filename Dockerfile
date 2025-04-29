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

# Copia aplicação
COPY . /var/www/html

# Define diretório de trabalho
WORKDIR /var/www/html

# Instala dependências
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Instala dependências do npm e gera build do frontend
RUN npm install && npm run build

# Copia arquivos de configuração
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf

# Permissões de pastas de cache e storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exposição de porta
EXPOSE 8080

# Inicializa supervisord (nginx + php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
