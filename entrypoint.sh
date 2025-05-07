#!/bin/sh

echo "✅ Ajustando permissões antes de iniciar supervisord..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Inicia supervisord normalmente (nginx + php-fpm + artisan etc)
exec /usr/bin/supervisord -c /etc/supervisord.conf
