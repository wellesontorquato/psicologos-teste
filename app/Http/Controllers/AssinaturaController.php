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
                'mensal' => 'price_1RHT4jFj0xelD5PU3nkqSMXm',
                'trimestral' => 'price_1RHT7GFj0xelD5PULmQUfjaH',
                'anual' => 'price_1RHT7oFj0xelD5PUNwBGHsYW',
            ]
        ]);
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();

        $validPrices = [
            'price_1RHT4jFj0xelD5PU3nkqSMXm', // mensal
            'price_1RHT7GFj0xelD5PULmQUfjaH', // trimestral
            'price_1RHT7oFj0xelD5PUNwBGHsYW', // anual
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
