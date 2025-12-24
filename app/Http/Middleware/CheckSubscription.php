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

        // ✅ Rotas que SEMPRE passam (evita loop e mantém /profile liberado)
        if (
            $request->routeIs('assinaturas.*') ||
            $request->routeIs('assinatura.*') ||
            $request->routeIs('billing.*') ||
            $request->is('stripe/*') ||
            $request->is('profile') ||
            $request->is('profile/*')
        ) {
            return $next($request);
        }

        // Admin sempre passa
        if ($user->is_admin) {
            return $next($request);
        }

        // Flag: usuário já teve alguma assinatura alguma vez?
        $temHistoricoAssinatura = $user->subscriptions()
            ->where('type', 'default')
            ->exists();

        // Pega a assinatura local mais recente do tipo default (pode estar cancelada/expirada)
        $sub = $user->subscriptions()
            ->where('type', 'default')
            ->latest('id')
            ->first();

        // Tenta buscar assinatura no Stripe e sincronizar "o mínimo necessário"
        $stripeSub = null;

        if ($user->stripe_id) {
            try {
                if ($sub && $sub->stripe_id) {
                    // Melhor: consulta a assinatura que já temos salva
                    $stripeSub = $sub->asStripeSubscription();
                } else {
                    // Fallback: lista e pega a mais relevante (a de maior current_period_end)
                    $stripe = Cashier::stripe();
                    $subs = $stripe->subscriptions->all([
                        'customer' => $user->stripe_id,
                        'status'   => 'all',
                        'limit'    => 10,
                    ]);

                    $stripeSub = collect($subs->data)
                        ->sortByDesc(fn ($s) => $s->current_period_end ?? 0)
                        ->first();
                }

                if ($stripeSub) {
                    $periodEnd = !empty($stripeSub->current_period_end)
                        ? Carbon::createFromTimestamp($stripeSub->current_period_end)
                        : null;

                    $cancelAtPeriodEnd = (bool) ($stripeSub->cancel_at_period_end ?? false);

                    // ✅ Se está cancelando ao fim do período, gravamos ends_at = current_period_end
                    // ✅ Se NÃO está cancelando ao fim do período, ends_at = null (recorrente)
                    // ✅ Se já está cancelada/encerrada e temos periodEnd, mantém ends_at = periodEnd
                    $endsAt = null;

                    if ($periodEnd) {
                        if ($cancelAtPeriodEnd) {
                            $endsAt = $periodEnd;
                        } elseif (in_array($stripeSub->status, ['canceled', 'incomplete_expired', 'unpaid'], true)) {
                            $endsAt = $periodEnd;
                        }
                    }

                    $sub = CashierSubscription::updateOrCreate(
                        ['stripe_id' => $stripeSub->id],
                        [
                            'user_id'       => $user->id,
                            'type'          => 'default',
                            'stripe_status' => $stripeSub->status,
                            'stripe_price'  => $stripeSub->items->data[0]->price->id ?? null,
                            'quantity'      => $stripeSub->items->data[0]->quantity ?? 1,
                            'trial_ends_at' => !empty($stripeSub->trial_end)
                                ? Carbon::createFromTimestamp($stripeSub->trial_end)
                                : null,
                            'ends_at'       => $endsAt,
                        ]
                    );

                    // Atualiza o flag pós-sync (caso ele nunca tenha tido assinatura local antes)
                    $temHistoricoAssinatura = $temHistoricoAssinatura || true;
                }
            } catch (\Throwable $e) {
                \Log::warning('Stripe sync falhou no middleware', [
                    'user_id' => $user->id,
                    'err'     => $e->getMessage(),
                ]);
            }
        }

        // ✅ Regra final de acesso:
        // - Trial (10 dias) libera acesso normalmente
        // - Assinatura válida libera
        // - Se tiver ends_at, só libera até ends_at
        $statusValido = $sub && in_array($sub->stripe_status, ['active', 'trialing', 'past_due'], true);
        $naJanela = $sub && (!$sub->ends_at || now()->lt($sub->ends_at));

        $temAcesso = $user->onTrial() || ($statusValido && $naJanela);

        if ($temAcesso) {
            return $next($request);
        }

        // ✅ Sem acesso:
        // - Se já teve assinatura alguma vez: manda pra "Minha Assinatura" (mostrar cancelada/expirada + faturas)
        // - Se nunca teve: manda pra página de planos (/assinaturas)
        if ($temHistoricoAssinatura) {
            return redirect()
                ->route('assinaturas.minha')
                ->with('error', 'Sua assinatura está cancelada ou expirada. Gerencie sua assinatura aqui.');
        }

        return redirect()
            ->route('assinaturas.index')
            ->with('error', 'Seu período de teste acabou. Escolha um plano para continuar.');
    }
}
