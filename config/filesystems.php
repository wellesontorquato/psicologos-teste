<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Define o disco padrão. Vamos deixar 'public' como padrão se você quiser
    | que uploads em geral vão direto pro volume persistente.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Aqui você configura os discos do Laravel.
    | ✅ O disco 'public' agora aponta para /var/www/html/data
    | que é o diretório do volume persistente montado.
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => '/data/public',
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],


        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('AWS_BUCKET', 'psigestor-files'),
            'url' => null,
            'endpoint' => env('AWS_ENDPOINT', 'https://usc1.contabostorage.com'),
            'use_path_style_endpoint' => true,
            'visibility' => env('AWS_VISIBILITY', 'public'),
            'throw' => true,
            'root' => env('AWS_ROOT', null),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | ✅ ATUALIZADO: aponta o link simbólico diretamente para o volume montado.
    | Isso garante que mesmo se alguém rodar 'php artisan storage:link', ele já
    | cria o link certo.
    |
    */

    'links' => [
        public_path('storage') => base_path('data/public'),
    ],


];
