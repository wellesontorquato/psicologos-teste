<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RequestIdMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $rid = $request->header('X-Request-Id') ?: (string) Str::uuid();

        $request->attributes->set('request_id', $rid);

        Log::withContext([
            'request_id' => $rid,
            'path' => $request->path(),
            'ip' => $request->ip(),
        ]);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $rid);

        return $response;
    }
}
