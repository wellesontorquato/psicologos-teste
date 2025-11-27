#!/bin/sh

echo "✅ Ajustando permissões antes de iniciar supervisord..."

# Garante permissões corretas para pastas internas do Laravel
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 🚀 EXECUTANDO MIGRATIONS 🚀
# O flag --force é essencial em produção para evitar a confirmação interativa.
echo "🔄 Aplicando migrations do Laravel (php artisan migrate --force)..."
php artisan migrate --force

# Apenas exibe logs úteis para debug
echo "ℹ️ Verificando conteúdo de /var/www/html/storage:"
ls -l /var/www/html/storage || echo "⚠️ Nenhum arquivo encontrado em storage."

# Inicia supervisord normalmente (nginx + php-fpm + outros serviços)
exec /usr/bin/supervisord -c /etc/supervisord.conf