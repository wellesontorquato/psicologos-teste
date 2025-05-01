<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\URL;

class ValidateSignatureIgnoringScheme
{
    public function handle($request, Closure $next)
    {
        // Aqui recriamos a URL **sem considerar o esquema (http ou https)**
        $url = $request->fullUrl();
        $signature = $request->query('signature');

        if (!$signature) {
            throw new InvalidSignatureException;
        }

        // Remove o signature da URL para comparar
        $original = str_replace('&signature=' . $signature, '', $url);
        $original = str_replace('?signature=' . $signature, '', $original);

        // Ignora o esquema (troca https por http)
        $httpUrl = preg_replace('/^https:/i', 'http:', $original);

        if (!URL::hasValidSignature($request, false)) {
            // Tenta validar **trocando o esquema para http**
            $temporaryRequest = $request->duplicate();
            $temporaryRequest->server->set('REQUEST_URI', parse_url($httpUrl, PHP_URL_PATH) . '?' . parse_url($httpUrl, PHP_URL_QUERY));

            if (!URL::hasValidSignature($temporaryRequest, false)) {
                throw new InvalidSignatureException;
            }
        }

        return $next($request);
    }
}
