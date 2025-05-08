#!/bin/sh

echo "✅ Ajustando permissões antes de iniciar supervisord..."

# Ajusta permissões normais (garante cache e logs funcionando)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage/logs /var/www/html/storage/app /var/www/html/bootstrap/cache

# ✅ NOVO: Se o volume está montado, garante a pasta public no /data e linka
if [ -d "/var/www/html/data" ]; then
    echo "🔗 Volume detectado em /var/www/html/data - preparando estrutura..."

    # Cria a pasta public e profile-photos dentro do volume, se não existir
    mkdir -p /var/www/html/data/public/profile-photos
    chown -R www-data:www-data /var/www/html/data

    # Se já existe o symlink antigo, remove para evitar conflito
    if [ -L "/var/www/html/storage/app/public" ]; then
        echo "♻️ Removendo symlink antigo de /storage/app/public"
        rm /var/www/html/storage/app/public
    fi

    # Cria o symlink para apontar para o volume
    echo "🔗 Criando novo symlink para /data/public"
    ln -s /var/www/html/data/public /var/www/html/storage/app/public

else
    echo "⚠️ Volume /var/www/html/data não detectado. Usando storage local padrão."

    # Garante que o symlink padrão existe (para /storage)
    if [ ! -L "/var/www/html/public/storage" ]; then
        echo "🔗 Criando symlink do storage padrão..."
        php artisan storage:link
    fi
fi

# 🔥 Start supervisord (php-fpm + nginx + cron jobs etc.)
exec /usr/bin/supervisord -c /etc/supervisord.conf
