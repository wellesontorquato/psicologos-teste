<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * As URIs que devem ser excluídas da verificação CSRF.
     *
     * @var array<int, string>
     */
    protected $except = [
        'stripe/*', // Stripe não envia CSRF token, precisamos liberar
    ];
}
