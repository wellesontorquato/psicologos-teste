<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Subscription as CashierSubscription;
use Carbon\Carbon;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Deixa passar páginas de planos e webhooks/Stripe, senão dá loop
        if ($request->routeIs('assinaturas.*') || $request->is('stripe/*')) {
            return $next($request);
        }

        // Admin sempre passa
        if ($user->is_admin) {
            return $next($request);
        }

        // 1) Tenta achar a assinatura "viva" localmente (mais recente e sem ends_at)
        $sub = $user->subscriptions()
            ->where('type', 'default')   // usa 'type' pois seu schema está assim
            ->whereNull('ends_at')
            ->latest('id')
            ->first();

        // 2) Se não achou, faz uma auto-reconciliação com a Stripe (uma vez por request)
        if (!$sub && $user->stripe_id) {
            try {
                $stripe = Cashier::stripe();
                $subs = $stripe->subscriptions->all([
                    'customer' => $user->stripe_id,
                    'status'   => 'all',
                    'limit'    => 10,
                ]);

                // Pega a assinatura "mais promissora" (ativa/trialing/past_due) mais recente
                $candidate = collect($subs->data)
                    ->filter(function ($s) {
                        return in_array($s->status, ['active', 'trialing', 'past_due'], true);
                    })
                    ->sortByDesc(function ($s) {
                        // ordena pela janela corrente mais longa (fim do período atual)
                        return $s->current_period_end ?? 0;
                    })
                    ->first();

                if ($candidate) {
                    // Garante que existe/atualiza a linha local desta assinatura
                    $sub = CashierSubscription::updateOrCreate(
                        ['stripe_id' => $candidate->id],
                        [
                            'user_id'        => $user->id,
                            'type'           => 'default', // seu schema
                            'stripe_status'  => $candidate->status, // 'active', 'trialing' ou 'past_due'
                            'stripe_price'   => $candidate->items->data[0]->price->id ?? null,
                            'quantity'       => $candidate->items->data[0]->quantity ?? 1,
                            'trial_ends_at'  => $candidate->trial_end ? Carbon::createFromTimestamp($candidate->trial_end) : null,
                            // ⚠️ zera ends_at se a assinatura está válida
                            'ends_at'        => in_array($candidate->status, ['active','trialing','past_due'], true) ? null : now(),
                        ]
                    );
                }
            } catch (\Throwable $e) {
                // Não quebre a navegação por erro de API; apenas logue
                \Log::warning('Auto-sync Stripe falhou no middleware', [
                    'user_id' => $user->id,
                    'err' => $e->getMessage(),
                ]);
            }
        }

        // 3) Regra final de acesso: status válido OU trial global ainda vigente
        $temAcesso = (
            $sub && in_array($sub->stripe_status, ['active', 'trialing', 'past_due'], true)
        ) || $user->onTrial();

        if ($temAcesso) {
            return $next($request);
        }

        return redirect()
            ->route('assinaturas.index')
            ->with('error', 'Você precisa ter uma assinatura ativa para acessar essa área.');
    }
}
