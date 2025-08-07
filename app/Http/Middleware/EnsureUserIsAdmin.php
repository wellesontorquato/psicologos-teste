<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || (int) auth()->user()->is_admin !== 1) {
            abort(403, 'Acesso restrito ao administrador.');
        }

        return $next($request);
    }
}
