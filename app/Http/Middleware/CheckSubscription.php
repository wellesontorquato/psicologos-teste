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

        // Admin sempre passa
        if ($user->is_admin) {
            return $next($request);
        }

        // Pega SEMPRE a assinatura mais recente não cancelada (ends_at = null)
        $sub = $user->subscriptions()
            ->where('type', 'default')
            ->whereNull('ends_at')
            ->latest('id')
            ->first();

        // Regra de acesso: status válido OU trial genérico ainda vigente
        $temAcesso = (
            $sub && in_array($sub->stripe_status, ['active', 'trialing', 'past_due'])
        ) || $user->onTrial();

        if ($temAcesso) {
            return $next($request);
        }

        return redirect()
            ->route('assinaturas.index')
            ->with('error', 'Você precisa ter uma assinatura ativa para acessar essa área.');
    }
}
