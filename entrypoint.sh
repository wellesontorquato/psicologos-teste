#!/bin/sh

echo "✅ Ajustando permissões antes de iniciar supervisord..."

# Ajusta permissões normais
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage/logs /var/www/html/storage/app /var/www/html/bootstrap/cache

# Novo: se /data está montado, cria estrutura padrão completa dentro dele
if mountpoint -q /var/www/html/data; then
    echo "🔗 Montagem detectada em /data - verificando estrutura"

    # Cria estrutura completa
    mkdir -p /var/www/html/data/public/profile-photos
    mkdir -p /var/www/html/data/private
    mkdir -p /var/www/html/data/logs

    echo "✅ Estrutura criada:"
    echo " - /data/public/profile-photos"
    echo " - /data/private"
    echo " - /data/logs"

    # Permissões
    chown -R www-data:www-data /var/www/html/data
    chmod -R 775 /var/www/html/data

    # Recria symlink de storage -> /data/public
    if [ -L "/var/www/html/storage/app/public" ]; then
        rm /var/www/html/storage/app/public
    fi
    ln -s /var/www/html/data/public /var/www/html/storage/app/public

    # Opcional: recria public/storage para garantir link correto
    if [ ! -L "/var/www/html/public/storage" ]; then
        echo "🔗 Criando symlink do storage público web..."
        ln -s /var/www/html/storage/app/public /var/www/html/public/storage
    fi

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
