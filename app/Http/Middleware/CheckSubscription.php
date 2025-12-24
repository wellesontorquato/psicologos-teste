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

        if ($request->routeIs('assinaturas.*') || $request->is('stripe/*')) {
            return $next($request);
        }

        if ($user->is_admin) {
            return $next($request);
        }

        // Pega a assinatura local mais recente do tipo default
        $sub = $user->subscriptions()
            ->where('type', 'default')
            ->latest('id')
            ->first();

        // Se não tem assinatura local, tenta olhar no Stripe
        $stripeSub = null;

        if ($user->stripe_id) {
            try {
                // Se temos uma stripe_id local, melhor ainda:
                if ($sub && $sub->stripe_id) {
                    $stripeSub = $sub->asStripeSubscription();
                } else {
                    // fallback: lista e escolhe a mais relevante
                    $stripe = Cashier::stripe();
                    $subs = $stripe->subscriptions->all([
                        'customer' => $user->stripe_id,
                        'status'   => 'all',
                        'limit'    => 10,
                    ]);

                    $stripeSub = collect($subs->data)
                        ->sortByDesc(fn($s) => $s->current_period_end ?? 0)
                        ->first();
                }

                // Se achou algo no Stripe, sincroniza o essencial no banco
                if ($stripeSub) {
                    $periodEnd = Carbon::createFromTimestamp($stripeSub->current_period_end);

                    $sub = CashierSubscription::updateOrCreate(
                        ['stripe_id' => $stripeSub->id],
                        [
                            'user_id'       => $user->id,
                            'type'          => 'default',
                            'stripe_status' => $stripeSub->status,
                            'stripe_price'  => $stripeSub->items->data[0]->price->id ?? null,
                            'quantity'      => $stripeSub->items->data[0]->quantity ?? 1,
                            'trial_ends_at' => $stripeSub->trial_end ? Carbon::createFromTimestamp($stripeSub->trial_end) : null,

                            // ✅ se está cancelando ao fim do período, gravamos ends_at = current_period_end
                            // ✅ se não, deixa ends_at NULL (assinatura recorrente)
                            'ends_at'       => ($stripeSub->cancel_at_period_end ?? false) ? $periodEnd : null,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                \Log::warning('Stripe sync falhou no middleware', [
                    'user_id' => $user->id,
                    'err' => $e->getMessage(),
                ]);
            }
        }

        // ✅ Regra final: tem acesso se:
        // - está em trial, OU
        // - assinatura está "valida" e (não tem ends_at) OU (agora < ends_at)
        $statusValido = $sub && in_array($sub->stripe_status, ['active', 'trialing', 'past_due'], true);
        $naJanela = $sub && (!$sub->ends_at || now()->lt($sub->ends_at));

        $temAcesso = $user->onTrial() || ($statusValido && $naJanela);

        if ($temAcesso) {
            return $next($request);
        }

        return redirect()
            ->route('assinaturas.index')
            ->with('error', 'Sua assinatura expirou. Assine para continuar.');
    }
}