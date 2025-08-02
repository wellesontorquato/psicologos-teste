<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Segurança básica
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');

        // Política de segurança de conteúdo (CSP)
        $csp = "default-src 'self' https:; ".
               "img-src 'self' data: https: usc1.contabostorage.com www.facebook.com; ".
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: cdn.jsdelivr.net cdnjs.cloudflare.com www.gstatic.com www.google.com connect.facebook.net www.facebook.com; ".
               "style-src 'self' 'unsafe-inline' https: cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com; ".
               "font-src 'self' data: https: fonts.gstatic.com cdnjs.cloudflare.com cdn.jsdelivr.net; ".
               "frame-src 'self' https://www.google.com https://www.facebook.com; ".
               "connect-src 'self' https:; ".
               "object-src 'none'; ".
               "frame-ancestors 'none';";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
