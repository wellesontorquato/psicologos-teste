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
use App\Http\Controllers\Api\FeriadosController;
use App\Http\Controllers\WppconnectDiagnosticoController;
use App\Http\Controllers\LembreteController;


// 🔍 Health check
Route::get('/ping', function () {
    return response()->json(['message' => 'API funcionando']);
});

Route::get('/feriados', [FeriadosController::class, 'index']);

Route::post('/webhook/whatsapp', [WebhookWhatsappController::class, 'receberMensagem'])
    ->name('webhook.whatsapp');
Route::get('/webhook/whatsapp/test-manual', [WebhookWhatsappController::class, 'testeManual']);
Route::get('/diagnostico-wpp', [WppconnectDiagnosticoController::class, 'executar']);
Route::get('/lembretes/testar', [LembreteController::class, 'enviarLembretesManualmente']);

Route::get('/health', function (Request $request) {
    return response()->json([
        'ok' => true,
        'service' => 'psigestor-api',
        'time' => now()->toIso8601String(),
        'request_id' => $request->attributes->get('request_id'),
    ]);
});

Route::get('/webhook/whatsapp/health', function (Request $request) {
    return response()->json([
        'ok' => true,
        'webhook' => 'whatsapp',
        'time' => now()->toIso8601String(),
        'request_id' => $request->attributes->get('request_id'),
    ]);
});


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