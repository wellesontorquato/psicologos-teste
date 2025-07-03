<?php

echo "🔍 SCANNER DE PROJETO LARAVEL PARA DEPLOY\n\n";

$checks = [
    'public/index.php' => 'Arquivo principal do Laravel (public/index.php)',
    'artisan' => 'Arquivo artisan (para comandos Laravel)',
    '.env' => 'Arquivo de ambiente (.env)',
    'storage' => 'Pasta storage (deve existir)',
    'bootstrap/cache' => 'Pasta bootstrap/cache (deve existir)',
    'composer.json' => 'Arquivo composer.json',
    'package.json' => 'Arquivo package.json',
    'nginx.conf' => 'Arquivo de configuração do nginx',
    'supervisord.conf' => 'Arquivo de configuração do supervisord',
    'entrypoint.sh' => 'Script de entrypoint',
    'Dockerfile' => 'Arquivo Dockerfile',
];

// Verificação de arquivos/pastas
foreach ($checks as $path => $description) {
    if (file_exists($path)) {
        echo "✅ Encontrado: $description\n";
    } else {
        echo "❌ Faltando: $description ($path)\n";
    }
}

// Verificação de permissões
$writableDirs = ['storage', 'bootstrap/cache'];
foreach ($writableDirs as $dir) {
    if (!file_exists($dir)) continue;
    if (is_writable($dir)) {
        echo "✅ Permissões OK: $dir está gravável\n";
    } else {
        echo "⚠️ Permissões incorretas: $dir NÃO está gravável\n";
    }
}

// Verifica se nginx.conf expõe porta 8080 e root correto
if (file_exists("nginx.conf")) {
    $nginx = file_get_contents("nginx.conf");
    if (strpos($nginx, 'listen 8080') !== false) {
        echo "✅ nginx.conf escutando na porta 8080\n";
    } else {
        echo "❌ nginx.conf NÃO está escutando na porta 8080\n";
    }

    if (strpos($nginx, 'root /var/www/html/public') !== false) {
        echo "✅ nginx.conf com root correto (/var/www/html/public)\n";
    } else {
        echo "❌ nginx.conf com root incorreto\n";
    }
}

// Verifica se php-fpm está escutando na porta 9000
$phpFpmPath = '/usr/local/etc/php-fpm.d/www.conf';
if (file_exists($phpFpmPath)) {
    $conf = file_get_contents($phpFpmPath);
    if (strpos($conf, 'listen = 127.0.0.1:9000') !== false) {
        echo "✅ PHP-FPM configurado para escutar 127.0.0.1:9000\n";
    } else {
        echo "❌ PHP-FPM pode estar escutando em porta errada (confira www.conf)\n";
    }
} else {
    echo "⚠️ Arquivo www.conf não encontrado no host. Pode estar acessível apenas no container.\n";
}

echo "\n🔁 SCAN FINALIZADO.\n";
