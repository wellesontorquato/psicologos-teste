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

        $token = '$2b$10$PKtItzgnFZpuYW1K9kVpdO_MNaKDApM5SmRQmH4O5Rz9F5n88WeHi';

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->post('http://localhost:21465/api/psicologo/send-message', [
            'phone' => $numero,
            'message' => $mensagem,
        ]);

        return back()->with('status', 'Resposta da API: ' . $response->body());
    }
}
