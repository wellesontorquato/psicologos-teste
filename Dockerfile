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

# Define timezone do sistema (America/Sao_Paulo)
RUN ln -snf /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime && echo "America/Sao_Paulo" > /etc/timezone

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia aplicação html 
COPY . /var/www/html

# Define diretório de trabalho
WORKDIR /var/www/html

# Instala dependências PHP e frontend
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN npm install && npm run build

# Copia arquivos de configuração
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf

# Cria o entrypoint reforçando permissões em toda inicialização
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Permissões iniciais durante o build
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Define limites de upload do PHP corretamente (acrescenta em vez de sobrescrever)
RUN echo "upload_max_filesize=15M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=16M" >> /usr/local/etc/php/conf.d/uploads.ini

# Expõe porta usada pelo nginx
EXPOSE 8080

# Usa o entrypoint para garantir permissões sempre que sobe
ENTRYPOINT ["/entrypoint.sh"]