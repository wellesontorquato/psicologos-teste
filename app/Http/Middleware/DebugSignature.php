<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class DebugSignature
{
    public function handle($request, Closure $next)
    {
        Log::info('[DEBUG Signature] Full URL: ' . $request->fullUrl());
        Log::info('[DEBUG Signature] Expected APP_URL: ' . config('app.url'));

        if (! URL::hasValidSignature($request)) {
            Log::error('[DEBUG Signature] INVALID SIGNATURE detected!');
            throw new InvalidSignatureException;
        }

        Log::info('[DEBUG Signature] Signature VALIDATED successfully.');
        return $next($request);
    }
}
