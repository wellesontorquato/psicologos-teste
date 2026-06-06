<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Credenciais de serviços externos utilizados pela aplicação.
    | As chaves reais devem ficar sempre no .env / Railway Variables.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | E-mail Providers
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Slack
    |--------------------------------------------------------------------------
    */

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WPPConnect
    |--------------------------------------------------------------------------
    */

    'wppconnect' => [
        'base_url' => env('WPPCONNECT_URL', 'http://127.0.0.1:3000'),
        'session'  => env('WPPCONNECT_SESSION', 'psigestor'),
        'token'    => env('WPPCONNECT_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google
    |--------------------------------------------------------------------------
    */

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Analytics
    |--------------------------------------------------------------------------
    */

    'ga' => [
        'measurement_id' => env('GA_MEASUREMENT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gemini
    |--------------------------------------------------------------------------
    */

    'gemini' => [
        'key'   => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    ],

];