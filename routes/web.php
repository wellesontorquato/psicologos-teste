<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\{
    ProfileController,
    SessaoController,
    EvolucaoController,
    DashboardController,
    AgendaController,
    PacienteController,
    ArquivoController,
    AuditController,
    UserController,
    WhatsappController,
    LembreteController,
    WebhookWhatsappController,
    WppconnectDiagnosticoController,
    NotificacaoController,
    AssinaturaController
};
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Testes internos / Healthchecks
|--------------------------------------------------------------------------
*/

Route::get('/_filesystem', function () {
    $path = base_path();
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    $files = [];
    foreach ($rii as $file) {
        if (!$file->isDir()) {
            $files[] = str_replace($path, '', $file->getPathname());
        }
    }
    return view('filesystem', ['files' => $files]);
});

Route::get('/run-migrate', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== env('MIGRATE_TOKEN')) {
        abort(403, 'Token inválido.');
    }
    Artisan::call('migrate', ['--force' => true]);
    return Response::make('Migração executada com sucesso!', 200);
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::get('/logs-debug/{file}', function ($file) {
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        abort(403, 'Acesso não autorizado.');
    }

    $allowed = [
        'artisan-setup.log',
        'artisan-setup-error.log',
        'artisan-setup-out.log',
    ];

    if (!in_array($file, $allowed)) {
        abort(404, 'Arquivo não permitido.');
    }

    $path = storage_path("logs/{$file}");

    if (!file_exists($path)) {
        return response('Log não encontrado.', 404);
    }

    return Response::make(file_get_contents($path), 200, [
        'Content-Type' => 'text/plain',
    ]);
})->where('file', '.*')->name('logs.debug.dynamic');


/*
|--------------------------------------------------------------------------
| Páginas públicas
|--------------------------------------------------------------------------
*/

Route::view('/', 'index')->name('home');
Route::view('/funcionalidades', 'pages.funcionalidades')->name('funcionalidades');
Route::view('/quem-somos', 'pages.quem-somos')->name('quem-somos');
Route::view('/contato', 'pages.contato')->name('contato');

/*
|--------------------------------------------------------------------------
| Área de Assinaturas (usuário autenticado, mas sem exigir assinatura ativa)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/assinaturas', [AssinaturaController::class, 'index'])->name('assinaturas.index');
    Route::post('/checkout', [AssinaturaController::class, 'checkout'])->name('assinatura.checkout');
    Route::view('/assinatura/sucesso', 'assinatura.sucesso')->name('assinaturas.sucesso');
    Route::view('/assinatura/cancelado', 'assinatura.cancelado')->name('assinaturas.cancelado');
});

/*
|--------------------------------------------------------------------------
| Perfil (usuário autenticado, verificado)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('update.photo');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    Route::delete('/photo', [ProfileController::class, 'deletePhoto'])->name('photo.delete');
});

/*
|--------------------------------------------------------------------------
| Área autenticada, verificada e com assinatura ativa
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', CheckSubscription::class])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/sessoes-json', [SessaoController::class, 'storeJson'])->name('sessoes.store.json');
    Route::put('/sessoes-json/{id}', [SessaoController::class, 'updateJson'])->name('sessoes.update.json');
    Route::get('/sessoes-json/{id}', [SessaoController::class, 'editJson'])->name('sessoes.editJson');

    Route::resources([
        'pacientes' => PacienteController::class,
        'evolucoes' => EvolucaoController::class,
    ]);

    Route::get('/dashboard/pdf', [DashboardController::class, 'exportarPdf'])->name('dashboard.pdf');
    Route::get('/dashboard/excel', [DashboardController::class, 'exportarExcel'])->name('dashboard.excel');
    Route::get('/pacientes/{paciente}/historico', [PacienteController::class, 'historico'])->name('pacientes.historico');
    Route::get('/pacientes/{paciente}/historico/pdf', [PacienteController::class, 'exportarHistoricoPdf'])->name('pacientes.historico.pdf');

    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda');
    Route::get('/api/sessoes', [AgendaController::class, 'eventos'])->name('agenda.eventos');

    Route::prefix('pacientes/{paciente}/arquivos')->group(function () {
        Route::get('/', [ArquivoController::class, 'index'])->name('arquivos.index');
        Route::post('/', [ArquivoController::class, 'store'])->name('arquivos.store');
    });
    Route::delete('/arquivos/{arquivo}', [ArquivoController::class, 'destroy'])->name('arquivos.destroy');
    Route::put('/arquivos/{arquivo}/renomear', [ArquivoController::class, 'renomear'])->name('arquivos.rename');

    /*
    |--------------------------------------------------------------------------
    | Área exclusiva para administradores
    |--------------------------------------------------------------------------
    */
    Route::middleware([EnsureUserIsAdmin::class])->group(function () {
        Route::get('/auditoria', [AuditController::class, 'index'])->name('auditoria.index');
        Route::get('/auditoria/exportar-pdf', [AuditController::class, 'exportarPdf'])->name('auditoria.exportar.pdf');
        Route::get('/auditoria/exportar-excel', [AuditController::class, 'exportarExcel'])->name('auditoria.exportar.excel');

        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::patch('/usuarios/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('usuarios.toggleAdmin');
    });

    Route::get('/sessoes/export', [SessaoController::class, 'export'])->name('sessoes.export');
    Route::resource('sessoes', SessaoController::class)->except(['show']);

    Route::get('/notificacoes', [NotificacaoController::class, 'dropdown'])->name('notificacoes.dropdown');
    Route::get('/notificacoes/acao/{id}', [NotificacaoController::class, 'acao'])->name('notificacoes.acao');
    Route::post('/notificacoes/ler-todas', [NotificacaoController::class, 'marcarTodasComoLidas'])->name('notificacoes.ler.todas');
    Route::get('/api/aniversariantes-hoje', [PacienteController::class, 'aniversariantesHoje'])->name('api.aniversariantes');
});

/*
|--------------------------------------------------------------------------
| Auth padrão do Laravel
|--------------------------------------------------------------------------
*/

Route::get('/force-clear', function () {
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');

    return 'Caches TOTALMENTE limpos 🚀!';
});

require __DIR__.'/auth.php';
