<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WppconnectDiagnosticoController extends Controller
{
    public function executar()
    {
        $token = config('services.wppconnect.token'); // Certifique-se de que está correto no config/services.php
        $session = 'psicologo';
        $baseUrl = 'http://localhost:21465/api/' . $session;

        $resultados = [];

        // 1. STATUS SESSION
        $status = Http::withHeaders([
            'Authorization' => "Bearer $token",
        ])->get("{$baseUrl}/status-session");

        $resultados['status-session'] = $status->json();

        // 2. START SESSION COM WEBHOOK
        $start = Http::withHeaders([
            'Authorization' => "Bearer $token",
            'Content-Type' => 'application/json',
        ])->post("{$baseUrl}/start-session", [
            'webhook' => route('webhook.whatsapp'), // certifique-se que essa rota existe no api.php
        ]);

        $resultados['start-session'] = $start->json();

        // 3. ENVIO DE MENSAGEM TESTE
        $mensagem = Http::withHeaders([
            'Authorization' => "Bearer $token",
        ])->post("{$baseUrl}/send-message", [
            'phone' => '5582999405099', // número de teste
            'message' => '✅ Diagnóstico Laravel: Teste de envio via controller.',
        ]);

        $resultados['envio-teste'] = $mensagem->json();

        return response()->json($resultados);
    }
}
