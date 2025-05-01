<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ValidateSignatureIgnoringScheme
{
    public function handle($request, Closure $next)
    {
        $signature = $request->query('signature');

        if (!$signature) {
            throw new InvalidSignatureException;
        }

        // Gera a URL sem o "signature" para validação
        $url = $request->fullUrl();
        $urlWithoutSignature = preg_replace('/(&|\?)signature=[^&]+/', '', $url);

        // 🔄 Remove http:// ou https:// para neutralizar esquema
        $urlToValidate = preg_replace('/^https?:\/\//i', '', $urlWithoutSignature);

        // Recalcula a assinatura manualmente (igual Laravel faz)
        $expectedSignature = hash_hmac('sha256', $urlToValidate, Config::get('app.key'));

        if (!hash_equals($expectedSignature, $signature)) {
            throw new InvalidSignatureException;
        }

        return $next($request);
    }
}
