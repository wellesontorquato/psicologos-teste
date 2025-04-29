<?php

return [

    'manifest_path' => 'build/manifest.json', // Certo

    'hot_file' => storage_path('app/vite.hot'), // Certo

    'build_directory' => 'build', // Certo

    'dev_server' => [
        'url' => env('VITE_DEV_SERVER_URL', 'http://localhost:5173'),
    ],

];
