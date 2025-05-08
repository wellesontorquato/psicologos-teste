#!/bin/sh

echo "✅ Ajustando permissões antes de iniciar supervisord..."

# ✅ Cria diretórios se não existirem (melhora robustez)
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/bootstrap/cache

# ✅ Ajusta permissões e donos
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 🔗 Refaz link do storage (evita problema em ambientes Railway/Docker)
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "🔗 Criando symlink do storage..."
    if php artisan storage:link; then
        echo "✅ Symlink criado com sucesso!"
    else
        echo "⚠️ Falha ao criar symlink (continuando mesmo assim)."
    fi
else
    echo "✅ Symlink já existe."
fi

# ✅ Teste se temos permissões corretas para escrever no log
touch /var/www/html/storage/logs/laravel.log || echo "⚠️ Não consegui criar/atualizar o arquivo de log!"

# 🔥 DEBUG extra: Mostra quem está rodando e permissões atuais
echo "👤 Usuário atual: $(whoami)"
ls -l /var/www/html/storage
ls -l /var/www/html/public

# ✅ Inicia supervisord normalmente (nginx + php-fpm + artisan etc)
exec /usr/bin/supervisord -c /etc/supervisord.conf
