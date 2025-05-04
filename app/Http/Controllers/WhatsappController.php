<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    public function formulario()
    {
        return view('teste-whatsapp');
    }

    public function enviar(Request $request)
    {
        $numero = $request->input('numero'); // Ex: 5582999405099
        $mensagem = $request->input('mensagem');

        $token = config('services.wppconnect.token');
        $url = config('services.wppconnect.url');
        $session = config('services.wppconnect.session');

        Http::withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->post("{$url}/api/{$session}/send-message", [
            'phone' => $numero,
            'message' => $mensagem,
        ]);

        return back()->with('status', 'Resposta da API: ' . $response->body());
    }
}
