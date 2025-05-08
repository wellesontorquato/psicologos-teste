#!/bin/sh

echo "✅ Ajustando permissões antes de iniciar supervisord..."

# Garante as pastas necessárias dentro de /data (volume Railway)
mkdir -p /data/logs
mkdir -p /data/app
mkdir -p /data/framework
mkdir -p /data/public

# Sincroniza do volume para o storage real (primeira carga)
echo "🔄 Sincronizando conteúdo de /data para /var/www/html/storage..."
rsync -a /data/ /var/www/html/storage/

# Garante permissões corretas no storage real
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Refaz link do storage (evita problema em ambientes Railway/Docker)
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "🔗 Criando symlink do storage..."
    php artisan storage:link
fi

# Inicializa supervisord normalmente (nginx + php-fpm + artisan etc)
echo "🚀 Iniciando supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
