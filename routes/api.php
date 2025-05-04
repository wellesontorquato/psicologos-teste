<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookWhatsappController;
use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Artisan;

// 🔍 Health check
Route::get('/ping', function () {
    return response()->json(['message' => 'API funcionando']);
});

// 📩 Webhook do WPPConnect para processar mensagens recebidas
Route::post('/webhook/whatsapp', [WebhookWhatsappController::class, 'receberMensagem'])
    ->name('webhook.whatsapp');

Route::get('/webhook/whatsapp/test-manual', [WebhookWhatsappController::class, 'testeManual']);

Route::get('/executar-schedule-seguro/{token}', function ($token) {
    if ($token !== env('TOKEN_CRON_SEGURA')) {
        abort(403, 'Acesso negado');
    }

    Artisan::call('schedule:run');

    return response()->json(['message' => 'Schedule executado com sucesso']);
});

