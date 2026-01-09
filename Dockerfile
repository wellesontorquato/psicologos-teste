FROM php:8.2-fpm

# ---------------------------------------------------------
# 1) Dependências do sistema + PHP extensions
# ---------------------------------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    unzip \
    git \
    curl \
    ca-certificates \
    gnupg \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo pdo_mysql zip gd bcmath \
  && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# 2) Node.js (recomendado)
# ---------------------------------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
  && apt-get update && apt-get install -y --no-install-recommends nodejs \
  && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# 3) Timezone
# ---------------------------------------------------------
RUN ln -snf /usr/share/zoneinfo/America/Recife /etc/localtime && echo "America/Recife" > /etc/timezone

# ---------------------------------------------------------
# 4) Composer
# ---------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------------------------------------------------------
# 5) App
# ---------------------------------------------------------
WORKDIR /var/www/html

# Copia o projeto TODO antes do composer install (evita erro de scripts)
COPY . /var/www/html

# Instala dependências PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Instala deps JS e build
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi \
  && npm run build || echo "ℹ️ npm run build não executado (sem script build?)"

# ---------------------------------------------------------
# 6) Configs
# ---------------------------------------------------------
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# ✅ Permissão do start-cron.sh
RUN chmod +x /var/www/html/start-cron.sh || true

# Permissões iniciais (o entrypoint reforça também)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Upload limits do PHP
RUN echo "upload_max_filesize=15M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=16M" >> /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
