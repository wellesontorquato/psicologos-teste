<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class DebugSignature
{
    public function handle($request, Closure $next)
    {
        Log::info('[DEBUG Signature] Full URL: ' . $request->fullUrl());

        // Primeira tentativa normal
        if (URL::hasValidSignature($request)) {
            Log::info('[DEBUG Signature] Signature VALIDATED successfully.');
            return $next($request);
        }

        // Se falhou, tenta com http (em caso de Railway forçando https)
        $url = $request->fullUrl();
        if (str_starts_with($url, 'https://')) {
            Log::warning('[DEBUG Signature] Tentando validar trocando https -> http');

            // Duplica a request e força a URL para http
            $httpUrl = preg_replace('/^https:/i', 'http:', $url);

            // Cria uma request temporária com a URL modificada
            $tempRequest = Request::create($httpUrl, $request->method());

            if (URL::hasValidSignature($tempRequest)) {
                Log::info('[DEBUG Signature] Validação BATEU ao trocar para HTTP!');
                return $next($request);
            }
        }

        // Se tudo falhar, erro
        Log::error('[DEBUG Signature] INVALID SIGNATURE detected mesmo após tentativa de correção!');
        throw new InvalidSignatureException;
    }
}
