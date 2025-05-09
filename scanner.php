<?php

$basePath = __DIR__;
$pattern = '/evolucoes/';
$filesWithMatch = [];

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));

foreach ($rii as $file) {
    if ($file->isDir()) {
        continue;
    }

    $filePath = $file->getPathname();
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);

    // Verifica apenas arquivos PHP e Blade
    if (in_array($extension, ['php', 'blade.php'])) {
        $contents = file_get_contents($filePath);

        // Procurar links diretos para /evolucoes/{alguma coisa}
        if (preg_match_all('/(href|action)\s*=\s*["\'].*\/evolucoes\/\d+/', $contents, $matches)) {
            $filesWithMatch[] = [
                'file' => $filePath,
                'match' => $matches[0],
            ];
        }

        // Procurar uso de route() apontando para evolucoes.show ou parecido
        if (preg_match_all('/route\s*\(\s*[\'"]evolucoes\.show/', $contents, $matches)) {
            $filesWithMatch[] = [
                'file' => $filePath,
                'match' => $matches[0],
            ];
        }

        // Procurar uso bruto de /evolucoes/ que não seja só listagem
        if (preg_match_all('/\/evolucoes\/\{\$?\w+/', $contents, $matches)) {
            $filesWithMatch[] = [
                'file' => $filePath,
                'match' => $matches[0],
            ];
        }
    }
}

// Exibe resultados
if (empty($filesWithMatch)) {
    echo "✅ Nenhum link ou rota suspeita para evolucoes/{id} encontrado.\n";
} else {
    echo "🔍 Foram encontrados os seguintes usos suspeitos:\n\n";
    foreach ($filesWithMatch as $entry) {
        echo "Arquivo: {$entry['file']}\n";
        foreach ($entry['match'] as $m) {
            echo "   ➔ {$m}\n";
        }
        echo "\n";
    }
}
