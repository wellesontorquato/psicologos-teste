#!/bin/bash

echo "========================"
echo "🔄 Limpando caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "🔑 Verificando APP_KEY..."
php artisan key:generate || echo "⚠️ APP_KEY já existente, pulando geração."

echo "🔄 Refazendo migrates..."
php artisan migrate:fresh --force

echo "🚀 Subindo projeto no Railway..."
railway up

echo "✅ Deploy completo!"
