<?php

function scanFiles($dir, $ignoreDirs = []) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    $files = [];
    foreach ($rii as $file) {
        if (!$file->isDir()) {
            $path = $file->getPathname();
            // Ignorar diretórios específicos
            foreach ($ignoreDirs as $ignoreDir) {
                if (strpos($path, DIRECTORY_SEPARATOR . $ignoreDir . DIRECTORY_SEPARATOR) !== false) {
                    continue 2; // pula esse arquivo
                }
            }
            $files[] = $path;
        }
    }
    return $files;
}

function searchInFile($file, $patterns) {
    $results = [];
    $contents = @file_get_contents($file);

    if ($contents === false) {
        return $results;
    }

    foreach ($patterns as $pattern) {
        if (strpos($contents, $pattern) !== false) {
            $results[] = $pattern;
        }
    }

    return $results;
}

$dir = __DIR__;
$ignoreDirs = ['wppconnect-server']; // <- adicionei aqui os diretórios a ignorar

$patterns = [
    '.vite',         // Ver se alguém usa .vite manualmente
    'build/.vite',   // Build errado
    'rollupOptions', // Configurações manuais de Vite que podem quebrar
    'manifest.json', // Manifesto errado
    'outDir',        // Vários outDir configurados
    'vite-plugin',   // Plugins estranhos
];

echo "🔎 Escaneando projeto em: $dir (ignorando " . implode(", ", $ignoreDirs) . ")\n\n";

$files = scanFiles($dir, $ignoreDirs);
$found = [];

foreach ($files as $file) {
    $matches = searchInFile($file, $patterns);
    if (!empty($matches)) {
        $found[] = [
            'file' => $file,
            'matches' => $matches
        ];
    }
}

if (empty($found)) {
    echo "✅ Nenhuma referência estranha encontrada!\n";
} else {
    echo "⚠️  Encontrado padrões suspeitos:\n";
    foreach ($found as $entry) {
        echo "- Arquivo: {$entry['file']}\n";
        foreach ($entry['matches'] as $match) {
            echo "  -> Contém: '{$match}'\n";
        }
        echo "\n";
    }
}

echo "🔍 Scan finalizado.\n";
?>
