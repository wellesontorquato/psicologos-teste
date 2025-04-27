<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookWhatsappController;
use App\Http\Controllers\PacienteController;


// 🔍 Health check
Route::get('/ping', function () {
    return response()->json(['message' => 'API funcionando']);
});

// 📩 Webhook do WPPConnect para processar mensagens recebidas
Route::post('/webhook/whatsapp', [WebhookWhatsappController::class, 'receberMensagem'])
    ->name('webhook.whatsapp');

Route::get('/webhook/whatsapp/test-manual', [WebhookWhatsappController::class, 'testeManual']);

