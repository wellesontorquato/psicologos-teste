#!/bin/sh
set -e

echo "✅ Entrypoint iniciado..."

# Descobre o diretório do projeto (Railway geralmente é /app)
APP_DIR="${APP_DIR:-$(pwd)}"

# Se existir /var/www/html e tiver Laravel lá, usa ele (compat)
if [ -d "/var/www/html" ] && [ -f "/var/www/html/artisan" ]; then
  APP_DIR="/var/www/html"
fi

echo "📁 APP_DIR = $APP_DIR"
cd "$APP_DIR"

echo "✅ Ajustando permissões..."
if [ -d "$APP_DIR/storage" ] && [ -d "$APP_DIR/bootstrap/cache" ]; then
  chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" || true
  chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" || true
else
  echo "⚠️ storage ou bootstrap/cache não encontrados em $APP_DIR"
fi

echo "🔄 Aplicando migrations (php artisan migrate --force)..."
php artisan migrate --force || echo "⚠️ Migrate falhou (continuando mesmo assim)."

echo "ℹ️ Verificando storage:"
ls -la "$APP_DIR/storage" || echo "⚠️ Nenhum arquivo encontrado em storage."

echo "🚀 Iniciando supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
