<?php

use App\Helpers\AssetHelper;

if (!function_exists('versao')) {
    function versao(string $path): string
    {
        return AssetHelper::versao($path);
    }
}
