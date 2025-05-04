<?php

// scanner.php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Carrega o .env manualmente:
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Pega configs manualmente:
$url = $_ENV['WPP_URL'] ?? 'NÃO DEFINIDO';
$token = $_ENV['WPP_TOKEN'] ?? 'NÃO DEFINIDO';
$session = $_ENV['WPP_SESSION'] ?? 'NÃO DEFINIDO';

// Mostra tudo:
echo "\n========= SCANNER =========\n";
echo "URL: {$url}\n";
echo "Session: {$session}\n";
echo "Token: {$token}\n";
echo "===========================\n";

// Monta dados:
$numero = '5582999405099';
$mensagem = '🛠 Teste via scanner.php';
$endpoint = "{$url}/api/{$session}/send-message";

echo "Enviando para: {$endpoint}\n";

// Prepara cURL:
$ch = curl_init($endpoint);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'phone' => $numero,
    'message' => $mensagem,
]));

// Executa:
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Fecha:
curl_close($ch);

// Resultado:
echo "Status: {$httpCode}\n";
echo "Body: {$response}\n";
echo "======= FIM DO SCANNER =======\n";
