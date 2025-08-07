<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class TrustProxies extends Middleware
{
    /**
     * Confia em todos os proxies (ideal para Railway, Heroku, etc.)
     *
     * @var array|string|null
     */
    protected $proxies = '*';

    /**
     * Usa todos os headers relevantes para detectar HTTPS corretamente
     *
     * @var int
     */
    protected $headers = SymfonyRequest::HEADER_X_FORWARDED_ALL;
}
