<?php

// Caminho base do projeto
$baseDir = __DIR__;

// Extensões de arquivos que queremos buscar (para não pegar lixo)
$allowedExtensions = ['php'];

// Palavra-chave a ser buscada
$needle = 'lembretes:enviar';

// Função para percorrer recursivamente
function searchInFiles($dir, $needle, $allowedExtensions)
{
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    foreach ($rii as $file) {
        if ($file->isDir()) {
            continue;
        }

        $ext = pathinfo($file->getPathname(), PATHINFO_EXTENSION);
        if (!in_array($ext, $allowedExtensions)) {
            continue;
        }

        $lines = file($file->getPathname());
        foreach ($lines as $num => $line) {
            if (stripos($line, $needle) !== false) {
                echo "\n📂 Arquivo: " . $file->getPathname() . " (linha " . ($num + 1) . ")\n";
                echo "👉 " . trim($line) . "\n";
            }
        }
    }
}

// Executa a busca
echo "🔎 Procurando por '{$needle}' em arquivos PHP...\n";
searchInFiles($baseDir, $needle, $allowedExtensions);

echo "\n✅ Scan finalizado.\n";
