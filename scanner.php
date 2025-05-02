<?php

echo "==== SCANNER INICIADO ====\n\n";

$paths = [
    'app/',
    'routes/',
    'resources/',
];

$patterns = [
    'sendEmailVerificationNotification',
    'event(new Registered',
    'Registered::class',
    'notify(',
    'CustomVerifyEmail',
];

foreach ($paths as $path) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

    foreach ($rii as $file) {
        if ($file->isDir()) continue;
        if (! in_array($file->getExtension(), ['php', 'blade.php'])) continue;

        $content = file_get_contents($file->getPathname());

        foreach ($patterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                echo ">>> [ENCONTRADO] {$pattern} em {$file->getPathname()}\n";
            }
        }
    }
}

echo "\n==== SCANNER FINALIZADO ====\n";
