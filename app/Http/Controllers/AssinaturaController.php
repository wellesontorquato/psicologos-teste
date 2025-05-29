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
                'mensal' => 'price_1RTtx2Fj0xelD5PUJlbowvRb',
                'trimestral' => 'price_1RTtxlFj0xelD5PUmhRGYz8D',
                'anual' => 'price_1RTtyKFj0xelD5PUU8G8XVfN',
            ]
        ]);
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();

        $validPrices = [
            'price_1RTtx2Fj0xelD5PUJlbowvRb', // mensal
            'price_1RTtxlFj0xelD5PUmhRGYz8D', // trimestral
            'price_1RTtyKFj0xelD5PUU8G8XVfN', // anual
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
}
