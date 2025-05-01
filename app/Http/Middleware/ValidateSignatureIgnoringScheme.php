<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\URL;

class ValidateSignatureIgnoringScheme
{
    public function handle($request, Closure $next)
    {
        if (URL::hasValidSignature($request, false)) {
            return $next($request);
        }

        // Segunda tentativa: troca http <-> https na URL para validar assinatura ignorando scheme
        $url = $request->fullUrl();
        $swappedUrl = preg_replace('/^http:/i', 'https:', $url);
        $swappedUrl = preg_replace('/^https:/i', 'http:', $swappedUrl);

        $temporaryRequest = $request->duplicate();
        $temporaryRequest->server->set('REQUEST_URI', parse_url($swappedUrl, PHP_URL_PATH) . '?' . parse_url($swappedUrl, PHP_URL_QUERY));

        if (!URL::hasValidSignature($temporaryRequest, false)) {
            throw new InvalidSignatureException;
        }

        return $next($request);
    }
}
