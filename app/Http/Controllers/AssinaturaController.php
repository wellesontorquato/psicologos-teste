<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssinaturaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('assinaturas', [
            'precos' => [
                'mensal'      => 'price_1RVxueC1nNYXXNDRXZRHr2N3',
                'trimestral'  => 'price_1RVxv5C1nNYXXNDRYJlrrwG5',
                'anual'       => 'price_1RVxvdC1nNYXXNDR2URxfXFz',
            ]
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate(['price_id' => 'required|string']);

        $user = Auth::user();

        // ✅ Evita criar múltiplas assinaturas "default" se já existe uma válida (ou em grace e ainda não expirou)
        $sub = $user->subscription('default');
        if ($sub) {
            $expirada = $sub->ends_at && now()->gte($sub->ends_at);

            if (!$expirada && ($sub->valid() || $sub->onGracePeriod())) {
                return redirect()->route('assinaturas.minha')
                    ->with('info', 'Você já possui uma assinatura. Gerencie por "Minha Assinatura".');
            }
        }

        $validPrices = [
            'price_1RVxueC1nNYXXNDRXZRHr2N3', // mensal
            'price_1RVxv5C1nNYXXNDRYJlrrwG5', // trimestral
            'price_1RVxvdC1nNYXXNDR2URxfXFz', // anual
        ];

        if (!in_array($request->price_id, $validPrices, true)) {
            abort(403, 'Plano inválido.');
        }

        return $user->newSubscription('default', $request->price_id)
            ->checkout([
                'success_url' => route('assinaturas.sucesso'),
                'cancel_url'  => route('assinaturas.cancelado'),
            ]);
    }

    public function minha()
    {
        $user = Auth::user();

        // pode ser null (usuário nunca assinou; trial ou pós-trial)
        $assinatura = $user->subscription('default');
        $faturas = $user->invoices();

        // flag pro blade decidir o que mostrar
        $temHistoricoAssinatura = $user->subscriptions()
            ->where('type', 'default')
            ->exists();

        return view('assinatura.minha', compact('assinatura', 'faturas', 'temHistoricoAssinatura'));
    }

    public function cancelar()
    {
        $user = Auth::user();
        $sub = $user->subscription('default');

        if (!$sub) {
            return redirect()->route('assinaturas.index')
                ->with('error', 'Você não possui assinatura.');
        }

        // ✅ Evita cancelar duas vezes (grace period ou já cancelada)
        if ($sub->onGracePeriod() || $sub->canceled()) {
            return redirect()->route('assinaturas.minha')
                ->with('info', 'Sua assinatura já está cancelada (ou programada para encerrar).');
        }

        // Cancela ao fim do período (padrão)
        $sub->cancel();

        // ✅ Puxa do Stripe e grava a data do fim do período (pra não depender de webhook)
        try {
            $stripeSub = $sub->asStripeSubscription();

            if (($stripeSub->cancel_at_period_end ?? false) && !empty($stripeSub->current_period_end)) {
                $sub->ends_at = \Carbon\Carbon::createFromTimestamp($stripeSub->current_period_end);
                $sub->save();
            }
        } catch (\Throwable $e) {
            \Log::warning('Falha ao sincronizar ends_at após cancelamento', [
                'user_id' => $user->id,
                'err'     => $e->getMessage(),
            ]);
        }

        return redirect()->route('assinaturas.minha')
            ->with('success', 'Assinatura cancelada. Você terá acesso até o fim do período atual.');
    }

    public function reativar()
    {
        $user = Auth::user();
        $assinatura = $user->subscription('default');

        if ($assinatura && $assinatura->onGracePeriod()) {

            // ✅ Se já expirou, não adianta "retomar"
            if ($assinatura->ends_at && now()->gte($assinatura->ends_at)) {
                return redirect()->route('assinaturas.minha')
                    ->with('error', 'Sua assinatura já expirou. Assine novamente para voltar a ter acesso.');
            }

            $assinatura->resume();

            return redirect()->route('assinaturas.minha')
                ->with('success', 'Sua assinatura foi reativada com sucesso.');
        }

        return redirect()->route('assinaturas.minha')
            ->with('error', 'Não foi possível reativar a assinatura.');
    }

    public function portal()
    {
        try {
            $user = Auth::user();

            return $user->redirectToBillingPortal(route('assinaturas.minha'));
        } catch (\Exception $e) {
            \Log::error('Stripe Billing Portal error: ' . $e->getMessage());

            return response()->json([
                'erro'     => true,
                'mensagem' => $e->getMessage()
            ], 500);
        }
    }
}
