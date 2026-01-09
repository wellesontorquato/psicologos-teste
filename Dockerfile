# Usa imagem base PHP oficial
FROM php:8.2-fpm

# ---------------------------------------------------------
# 1) Dependências do sistema + Nginx + Supervisor + Node.js
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

# Instala Node.js + npm (mais confiável do que "npm" via apt em várias imagens)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
  && apt-get update && apt-get install -y --no-install-recommends nodejs \
  && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# 2) Timezone do sistema
# ---------------------------------------------------------
# (Recife e São Paulo usam o mesmo offset na prática, mas deixei Recife pra bater com seus crons)
RUN ln -snf /usr/share/zoneinfo/America/Recife /etc/localtime && echo "America/Recife" > /etc/timezone

# ---------------------------------------------------------
# 3) Composer
# ---------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------------------------------------------------------
# 4) App
# ---------------------------------------------------------
WORKDIR /var/www/html

# Copia primeiro os manifests pra aproveitar cache de camadas
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Copia manifests do Node pra cache
COPY package.json package-lock.json* ./

# Instala deps JS (se tiver lockfile usa ci, senão install)
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

# Agora copia o restante do projeto
COPY . /var/www/html

# Build do frontend (se seu projeto tiver build)
RUN npm run build || echo "ℹ️ npm run build não executado (sem script build?)"

# ---------------------------------------------------------
# 5) Configs (nginx/supervisor/entrypoint)
# ---------------------------------------------------------
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# ✅ Permissão do start-cron.sh (como você pediu)
RUN chmod +x /var/www/html/start-cron.sh || true

# Permissões iniciais durante o build (e reforça no entrypoint também)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Upload limits do PHP
RUN echo "upload_max_filesize=15M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=16M" >> /usr/local/etc/php/conf.d/uploads.ini

# Railway geralmente injeta PORT; nginx precisa ouvir nessa porta.
EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]

# Supervisord sobe nginx + php-fpm + queue + cron-js
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
