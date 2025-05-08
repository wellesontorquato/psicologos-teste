#!/bin/sh

echo "✅ Ajustando permissões antes de iniciar supervisord..."

# Ajusta permissões normais do Laravel (sempre bom)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage/logs /var/www/html/storage/app /var/www/html/bootstrap/cache

# Novo: se /data está montado, cria estrutura padrão completa dentro dele
if mountpoint -q /data; then
    echo "🔗 Montagem detectada em /data - verificando estrutura"

    # Cria estrutura completa SE não existir
    mkdir -p /data/public/profile-photos
    mkdir -p /data/private
    mkdir -p /data/logs

    # ⚠️ REFORÇA as permissões TODA VEZ (mesmo que já exista)
    echo "🔧 Ajustando permissões do volume /data..."
    chown -R www-data:www-data /data
    chmod -R 775 /data

    # Recria symlink de storage -> /data/public
    if [ -L "/var/www/html/storage/app/public" ]; then
        rm /var/www/html/storage/app/public
    fi
    ln -s /data/public /var/www/html/storage/app/public

    # Opcional: recria public/storage para garantir link correto
    if [ ! -L "/var/www/html/public/storage" ]; then
        echo "🔗 Criando symlink do storage público web..."
        ln -s /data/public /var/www/html/public/storage
    fi

else
    echo "⚠️ Volume /data não montado. Usando storage local padrão."

    if [ ! -L "/var/www/html/public/storage" ]; then
        echo "🔗 Criando symlink do storage padrão..."
        php artisan storage:link
    fi
fi

# ✅ Log para confirmar antes de rodar supervisord
echo "📂 Conteúdo atual de /data/public:"
ls -l /data/public || echo "❌ /data/public não encontrado"

# Inicia supervisord normalmente
exec /usr/bin/supervisord -c /etc/supervisord.conf
