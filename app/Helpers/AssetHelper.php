<?php

namespace App\Helpers;

class AssetHelper
{
    /**
     * Gera o caminho do asset com versionamento baseado em filemtime()
     */
    public static function versao(string $path): string
    {
        $fullPath = public_path($path);
        $versao = file_exists($fullPath) ? filemtime($fullPath) : time();
        return asset($path) . '?v=' . $versao;
    }
}
