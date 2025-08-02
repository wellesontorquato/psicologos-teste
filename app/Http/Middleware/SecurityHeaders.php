<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Impede downgrade para HTTP
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // Evita clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // Evita detecção de tipo de conteúdo errado
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Remove referrer sensível em downgrades
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');

        // Impede compartilhamento indevido entre janelas/abas
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');

        // Definição da política CSP
        $csp = "default-src 'self' https:; ".
               "img-src 'self' data: https: usc1.contabostorage.com; ".
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: cdn.jsdelivr.net cdnjs.cloudflare.com www.gstatic.com www.google.com connect.facebook.net; ".
               "style-src 'self' 'unsafe-inline' https: cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com; ".
               "font-src 'self' data: https: fonts.gstatic.com cdnjs.cloudflare.com cdn.jsdelivr.net; ".
               "frame-src 'self' https://www.google.com https://www.facebook.com; ".
               "connect-src 'self' https:; ".
               "object-src 'none'; ".
               "frame-ancestors 'none'; ".
               "report-uri /csp-report; ".
               "report-to csp-endpoint;";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
