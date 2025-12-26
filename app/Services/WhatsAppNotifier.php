<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotifier
{
    public function send(string $message): bool
    {
        $baseUrl = rtrim(env('WPPCONNECT_URL'), '/');
        $session = env('WPPCONNECT_SESSION');
        $token   = env('WPPCONNECT_TOKEN');

        if (!$baseUrl || !$session || !$token) {
            Log::warning('[WPP] Variáveis ausentes', compact('baseUrl', 'session'));
            return false;
        }

        $url = "{$baseUrl}/api/{$session}/send-message";

        $payload = [
            'phone'   => env('WPP_TO_ADMIN', '5582999405099'),
            'message' => $message,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ])->timeout(15)->post($url, $payload);

            if (!$response->successful()) {
                Log::error('[WPP] Erro ao enviar mensagem', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('[WPP] Exceção ao enviar mensagem', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
//FIM DO CÓDIGO//