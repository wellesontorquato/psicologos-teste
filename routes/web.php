<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Laravel\Cashier\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\NewsController;
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
    AssinaturaController,
    BlogController
};

/*
|--------------------------------------------------------------------------
| Testes internos / Healthchecks
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

/*
|--------------------------------------------------------------------------
| Páginas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $news = \App\Models\News::latest()->take(3)->get();
    return view('index', compact('news'));
})->name('home');

Route::view('/funcionalidades', 'pages.funcionalidades')->name('funcionalidades');
Route::view('/planos', 'pages.planos')->name('planos');
Route::view('/quem-somos', 'pages.quem-somos')->name('quem-somos');
Route::view('/contato', 'pages.contato')->name('contato');
Route::post('/contato/enviar', [App\Http\Controllers\ContatoController::class, 'enviar'])->name('contato.enviar');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

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

    Route::get('/minha-assinatura', [AssinaturaController::class, 'minha'])->name('assinaturas.minha');
    Route::post('/cancelar-assinatura', [AssinaturaController::class, 'cancelar'])->name('assinatura.cancelar');

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

    Route::resource('pacientes', PacienteController::class);
    
    Route::resource('evolucoes', EvolucaoController::class)->parameters([
        'evolucoes' => 'evolucao',
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
    Route::middleware([EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/auditoria', [AuditController::class, 'index'])->name('auditoria.index');
        Route::get('/auditoria/exportar-pdf', [AuditController::class, 'exportarPdf'])->name('auditoria.exportar.pdf');
        Route::get('/auditoria/exportar-excel', [AuditController::class, 'exportarExcel'])->name('auditoria.exportar.excel');

        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::patch('/usuarios/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('usuarios.toggleAdmin');

        // ✅ Aqui as rotas de news ganham prefixo admin e nome admin.news.*
        Route::resource('news', NewsController::class);
    });

    Route::get('/sessoes/export', [SessaoController::class, 'export'])->name('sessoes.export');
    Route::post('/sessoes/gerar-recorrencias', [SessaoController::class, 'gerarRecorrencias'])->name('sessoes.gerarRecorrencias');
    Route::resource('sessoes', SessaoController::class)->except(['show']);

    Route::get('/notificacoes', [NotificacaoController::class, 'dropdown'])->name('notificacoes.dropdown');
    Route::get('/notificacoes/{id}/acao', [NotificacaoController::class, 'acao'])->name('notificacoes.acao');
    Route::post('/notificacoes/ler-todas', [NotificacaoController::class, 'marcarTodasComoLidas'])->name('notificacoes.ler.todas');
    Route::get('/api/aniversariantes-hoje', [PacienteController::class, 'aniversariantesHoje'])->name('api.aniversariantes');
});

/*
|--------------------------------------------------------------------------
| Stripe Webhook
|--------------------------------------------------------------------------
| Responsável por receber os eventos da Stripe e salvar as assinaturas no banco.
| Não requer autenticação.
*/
    Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);

/*
|--------------------------------------------------------------------------
| Auth padrão do Laravel
|--------------------------------------------------------------------------
*/

// Route::get('/force-clear', function () {
//     Artisan::call('config:clear');
//     Artisan::call('route:clear');
//     Artisan::call('cache:clear');
//     Artisan::call('view:clear');

//     return 'Caches TOTALMENTE limpos 🚀!';
// });

// // Crie rapidamente um comando em routes/web.php só para forçar isso:
//     Route::get('/fix-perms', function() {
//         exec('chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache');
//         return 'Permissões corrigidas!';
//     });    

//     // routes/web.php (temporário)
// Route::get('/check-logs', function() {
//     $logFile = storage_path('logs/laravel.log');
//     $owner = posix_getpwuid(fileowner($logFile));
//     $group = posix_getgrgid(filegroup($logFile));
//     return response()->json([
//         'current_user' => get_current_user(),
//         'effective_user' => posix_getpwuid(posix_geteuid()),
//         'log_file' => $logFile,
//         'file_owner' => $owner,
//         'file_group' => $group,
//         'file_perms' => substr(sprintf('%o', fileperms($logFile)), -4),
//         'is_writable' => is_writable($logFile),
//     ]);
// });

// Route::get('/logs-debug/{file}', function ($file) {
//     if (!auth()->check() || !auth()->user()->isAdmin()) {
//         abort(403, 'Acesso não autorizado.');
//     }

//     $allowed = [
//         'artisan-setup.log',
//         'artisan-setup-error.log',
//         'artisan-setup-out.log',
//         'laravel.log',
//     ];

//     if (!in_array($file, $allowed)) {
//         abort(404, 'Arquivo não permitido.');
//     }

//     $path = storage_path("logs/{$file}");

//     if (!file_exists($path)) {
//         return response('Log não encontrado.', 404);
//     }

//     return Response::make(file_get_contents($path), 200, [
//         'Content-Type' => 'text/plain',
//     ]);
// })->where('file', '.*')->name('logs.debug.dynamic');

// Route::get('/force-test-upload', function () {
//     $path = Storage::disk('s3')->put('profile-photos/teste-remote.txt', 'Arquivo criado no container remoto 🚀');

//     return response()->json([
//         'message' => 'Arquivo criado remotamente!',
//         'path' => $path,
//         'full_path' => Storage::disk('s3')->path('profile-photos/teste-remote.txt'),
//     ]);
// });

Route::get('/testar-log-whatsapp', function () {
    Log::channel('whatsapp')->info('Mensagem de teste gravada no canal whatsapp em ' . now());
    return 'Log de teste enviado!';
});

Route::get('/ver-log-whatsapp', function () {
    $logFile = storage_path('logs/whatsapp.log');
    $logContent = file_exists($logFile) ? file_get_contents($logFile) : 'Nenhum log encontrado.';
    return "<pre style='background:#111;color:#0f0;padding:20px;'>".e($logContent)."</pre>";
    })->middleware('auth');

Route::get('/_filesystem', function () {
    $path = base_path();
    $files = [];

   $iterator = new RecursiveIteratorIterator(
         new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            function ($current, $key, $iterator) {
               // 🔒 Ignorar vendor, node_modules, data/lost+found e tudo que começa com ponto
                $pathname = $current->getPathname();
                if (
                  strpos($pathname, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false ||
                     strpos($pathname, DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR) !== false ||
                    strpos($pathname, DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'lost+found') !== false ||
                    strpos($current->getFilename(), '.') === 0 // ignora arquivos ocultos e .git etc
                ) {
                    return false;
                }

               // Se não conseguir ler o diretório, ignora tambémmm
                 if ($current->isDir() && !is_readable($pathname)) {
                     return false;
                 }

                 return true;
             }
         ),
         RecursiveIteratorIterator::SELF_FIRST
     );

     foreach ($iterator as $file) {
         if ($file->isFile()) {
             try {
                 $fullPath = $file->getPathname();
                 $relativePath = str_replace($path, '', $fullPath);

                $files[] = [
                    'path' => $relativePath,
                     'last_modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
                     'size' => $file->getSize(),
                 ];
             } catch (Exception $e) {
                 // Se não conseguir acessar, ignora silenciosamente
                 continue;
             }
         }
     }

     return view('filesystem', ['files' => $files]);
 })->name('filesystem.index');


 Route::get('/_filesystem/view', function (\Illuminate\Http\Request $request) {
     $file = $request->query('file');
     if (!$file) {
         abort(404, 'Arquivo não especificado.');
     }

     $fullPath = base_path($file);

     if (!file_exists($fullPath)) {
         abort(404, 'Arquivo não encontrado.');
     }

     return Response::file($fullPath);
 })->name('filesystem.view');

 Route::get('/_filesystem/download', function (\Illuminate\Http\Request $request) {
     $file = $request->query('file');
     if (!$file) {
         abort(404, 'Arquivo não especificado.');
     }

     $fullPath = base_path($file);

     if (!file_exists($fullPath)) {
         abort(404, 'Arquivo não encontrado.');
     }

     return Response::download($fullPath);
 })->name('filesystem.download');

// Route::get('/run-migrate', function (\Illuminate\Http\Request $request) {
//     if ($request->query('token') !== env('MIGRATE_TOKEN')) {
//         abort(403, 'Token inválido.');
//     }
//     Artisan::call('migrate', ['--force' => true]);
//     return Response::make('Migração executada com sucesso!', 200);
// });


require __DIR__.'/auth.php';
