#!/bin/sh

echo "✅ Ajustando permissões antes de iniciar supervisord..."

# Ajusta permissões normais
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage/logs /var/www/html/storage/app /var/www/html/bootstrap/cache

# Novo: se /data está montado, cria a pasta public dentro dele e linka
if mountpoint -q /data; then
    echo "🔗 Montagem detectada em /data - redirecionando storage público"

    # Cria a pasta profile-photos dentro do volume se não existir
    mkdir -p /data/public/profile-photos
    chown -R www-data:www-data /data

    # Remove o storage atual SE for symlink antigo
    if [ -L "/var/www/html/storage/app/public" ]; then
        rm /var/www/html/storage/app/public
    fi

    # Cria o symlink para /data/public
    ln -s /data/public /var/www/html/storage/app/public
else
    echo "⚠️ Volume /data não montado. Usando storage local padrão."

    # Faz o link normal se não tiver volume
    if [ ! -L "/var/www/html/public/storage" ]; then
        echo "🔗 Criando symlink do storage padrão..."
        php artisan storage:link
    fi
fi

# Inicia supervisord normalmente
exec /usr/bin/supervisord -c /etc/supervisord.conf
