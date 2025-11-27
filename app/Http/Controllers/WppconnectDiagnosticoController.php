<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WppconnectDiagnosticoController extends Controller
{
    public function executar()
    {
        // CONFIG WPPConnect via services.php
        $token    = config('services.wppconnect.token');
        $baseUrl  = rtrim(config('services.wppconnect.base_url'), '/');
        $session  = 'psigestor';

        $apiBase = "{$baseUrl}/api/{$session}";
        $resultados = [];

        Log::info('[Diagnóstico WPPConnect] Iniciando diagnóstico...');

        // -------------------------------------------------------------------
        // 1. STATUS DA SESSÃO
        // -------------------------------------------------------------------
        try {
            $status = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept'        => 'application/json',
            ])->get("{$apiBase}/status-session");

            $resultados['status-session'] = $status->json();
        } catch (\Exception $e) {
            $resultados['status-session'] = [
                'error' => $e->getMessage(),
            ];
        }

        // -------------------------------------------------------------------
        // 2. START SESSION COM WEBHOOK
        // -------------------------------------------------------------------
        try {
            $start = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept'        => 'application/json',
            ])->post("{$apiBase}/start-session", [
                'webhook' => route('webhook.whatsapp'), // sua rota no api.php
            ]);

            $resultados['start-session'] = $start->json();
        } catch (\Exception $e) {
            $resultados['start-session'] = [
                'error' => $e->getMessage(),
            ];
        }

        // -------------------------------------------------------------------
        // 3. ENVIO DE MENSAGEM TESTE
        // -------------------------------------------------------------------
        try {
            $mensagem = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept'        => 'application/json',
            ])->post("{$apiBase}/send-message", [
                'phone'   => '5582999405099', // número de teste
                'message' => '✅ Diagnóstico Laravel: Teste de envio via controller.',
            ]);

            $resultados['envio-teste'] = $mensagem->json();
        } catch (\Exception $e) {
            $resultados['envio-teste'] = [
                'error' => $e->getMessage(),
            ];
        }

        // LOG FINAL
        Log::info('[Diagnóstico WPPConnect] Resultado:', $resultados);

        return response()->json($resultados);
    }
}
    