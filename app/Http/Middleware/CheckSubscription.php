<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user->is_admin) {
            return $next($request);
        }

        if ($user->subscribed('default') || $user->onTrial()) {
            return $next($request);
        }

        return redirect()->route('assinaturas.index')
            ->with('error', 'Você precisa ter uma assinatura ativa para acessar essa área.');
    }
}
