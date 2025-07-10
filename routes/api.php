<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\WebhookWhatsappController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\SessaoController;
use App\Http\Controllers\EvolucaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BlogController;

// 🔍 Health check
Route::get('/ping', function () {
    return response()->json(['message' => 'API funcionando']);
});

Route::post('/webhook/whatsapp', [WebhookWhatsappController::class, 'receberMensagem']);

Route::match(['get', 'post'], '/webhook/whatsapp/debug', function (Request $request) {
    $method = $request->method();

    if ($method === 'POST') {
        Log::channel('whatsapp')->info('[✅ VENOM POST] Webhook recebido com dados', [
            'from' => $request->input('from'),
            'body' => $request->input('body'),
            'timestamp' => $request->input('timestamp'),
            'headers' => $request->headers->all(),
        ]);
    } else {
        Log::channel('whatsapp')->info('[🧪 DEBUG VENOM] Webhook GET recebido', [
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
        ]);
    }

    return response()->json(['ok' => true]);
});

Route::any('/webhook/whatsapp/force', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Log::channel('whatsapp')->info('[🛠️ FALLBACK ANY] Webhook recebido com método: ' . $request->method(), [
        'headers' => $request->headers->all(),
        'body_raw' => $request->getContent(),
        'all_inputs' => $request->all(),
    ]);
    return response()->json(['message' => 'Webhook forçado recebido.'], 200);
});


Route::get('/webhook/whatsapp/test-manual', [WebhookWhatsappController::class, 'testeManual']);
Route::get('/diagnostico-webhook', [WebhookWhatsappController::class, 'diagnosticarWebhook']);


Route::get('/executar-schedule-seguro/{token}', function ($token) {
    if ($token !== env('TOKEN_CRON_SEGURA')) {
        abort(403, 'Acesso negado');
    }
    Log::info('[Scheduler] Chamou o schedule:run via webhook');
    return response()->json(['message' => 'Schedule e lembretes executados com sucesso']);
});

// 🔐 ROTAS DE AUTENTICAÇÃO COM SANCTUM
Route::post('/login', [AuthController::class, 'login']);

// 🔓 ROTA DE BLOG PÚBLICA (ACESSO LIVRE PARA CARROSSEL NA HOMEPAGE)
Route::get('/blog-json', [BlogController::class, 'apiIndex']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // 📊 Dashboard
    Route::get('/dashboard', function (Request $request) {
        $controller = new DashboardController();
        return response()->json($controller->obterDadosDashboard($request));
    });

    // 👥 Pacientes
    Route::get('/pacientes', [PacienteController::class, 'indexJson']);

    // 📅 Sessões
    Route::get('/sessoes', [SessaoController::class, 'index']);

    // 📝 Evoluções
    Route::get('/evolucoes', [EvolucaoController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | 🔄 CRUD JSON (Flutter)
    |--------------------------------------------------------------------------
    */

    // 📅 Sessões JSON
    Route::get('/sessoes-json', [SessaoController::class, 'indexJson']);
    Route::post('/sessoes-json', [SessaoController::class, 'storeJson']);
    Route::post('/sessoes-json/recorrencias', [SessaoController::class, 'gerarRecorrenciasJson']);
    Route::put('/sessoes-json/{id}', [SessaoController::class, 'updateJson']);
    Route::delete('/sessoes-json/{id}', [SessaoController::class, 'destroyJson']);
    Route::get('/sessoes-json/{id}', [SessaoController::class, 'show']);

    // 📝 Evoluções JSON
    Route::get('/evolucoes-json', [EvolucaoController::class, 'indexJson']);
    Route::post('/evolucoes-json', [EvolucaoController::class, 'storeJson']);
    Route::put('/evolucoes-json/{id}', [EvolucaoController::class, 'updateJson']);
    Route::delete('/evolucoes-json/{id}', [EvolucaoController::class, 'destroyJson']);

    // 👥 Pacientes JSON
    Route::get('/pacientes-json', [PacienteController::class, 'indexJson']);
    Route::post('/pacientes-json', [PacienteController::class, 'storeJson']);
    Route::put('/pacientes-json/{id}', [PacienteController::class, 'updateJson']);
    Route::delete('/pacientes-json/{id}', [PacienteController::class, 'destroyJson']);
    Route::get('/pacientes-json/{id}', [PacienteController::class, 'showJson']);
});