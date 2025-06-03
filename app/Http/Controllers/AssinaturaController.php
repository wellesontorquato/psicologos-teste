<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssinaturaController extends Controller
{
    public function index()
    {
        return view('assinaturas', [
            'precos' => [
                'mensal' => 'price_1RVxueC1nNYXXNDRXZRHr2N3',
                'trimestral' => 'price_1RVxv5C1nNYXXNDRYJlrrwG5',
                'anual' => 'price_1RVxvdC1nNYXXNDR2URxfXFz',
            ]
        ]);
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();

        $validPrices = [
            'price_1RVxueC1nNYXXNDRXZRHr2N3', // mensal
            'price_1RVxv5C1nNYXXNDRYJlrrwG5', // trimestral
            'price_1RVxvdC1nNYXXNDR2URxfXFz', // anual
        ];

        if (!in_array($request->price_id, $validPrices)) {
            abort(403, 'Plano inválido.');
        }

        return $user->newSubscription('default', $request->price_id)
            ->checkout([
                'success_url' => route('assinaturas.sucesso'),
                'cancel_url' => route('assinaturas.cancelado'),
            ]);
    }

    public function minha()
    {
        $user = Auth::user();
        $assinatura = $user->subscription('default');

        if (!$assinatura) {
            return redirect()->route('assinaturas.index')->with('error', 'Você ainda não possui uma assinatura ativa.');
        }

        $faturas = $user->invoices(); // Laravel Cashier pega via Stripe

        return view('assinatura.minha', compact('assinatura', 'faturas'));
    }

    public function cancelar()
    {
        $user = Auth::user();

        if ($user->subscribed('default')) {
            $user->subscription('default')->cancel();
            return redirect()->route('assinaturas.minha')->with('success', 'Assinatura cancelada com sucesso.');
        }

        return redirect()->route('assinaturas.index')->with('error', 'Você não possui uma assinatura ativa.');
    }
}
