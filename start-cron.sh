#!/bin/sh
set -e

echo "✅ start-cron.sh: iniciando node-cron..."
echo "📁 PWD inicial: $(pwd)"
echo "🕒 DATE: $(date)"

# Resolve diretório do app de forma segura
APP_DIR="${APP_DIR:-/var/www/html}"

if [ -f "/app/artisan" ]; then
  APP_DIR="/app"
elif [ -f "/var/www/html/artisan" ]; then
  APP_DIR="/var/www/html"
fi

echo "📁 APP_DIR resolvido: $APP_DIR"

cd "$APP_DIR"

# sanity checks (log claro se algo estiver errado)
if [ ! -f "cron.cjs" ]; then
  echo "❌ cron.cjs não encontrado em $APP_DIR"
  exit 1
fi

if ! command -v node >/dev/null 2>&1; then
  echo "❌ Node.js não encontrado no PATH"
  exit 1
fi

node cron.cjs
