<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Laravel\Cashier\Http\Controllers\WebhookController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\Admin\NewsController;
use App\Models\Paciente;
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
    BlogController,
    LandingPageController,
    FileProxyController,
    ClinicalCopilotController
};
use App\Models\News;
use App\Jobs\SyncUserCalendar;

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
| Endpoint para relatórios CSP
|--------------------------------------------------------------------------
*/
Route::post('/csp-report', function (Request $request) {
    Log::warning('[CSP VIOLATION]', $request->all());
    return response()->json(['status' => 'CSP report received']);
});

/*
|--------------------------------------------------------------------------
| Sitemap XML
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', function () {
    $urls = [
        url('/'),
        url('/funcionalidades'),
        url('/planos'),
        url('/quem-somos'),
        url('/contato'),
        url('/blog'),
        url('/politica-de-privacidade'),
        url('/termos-de-uso'),
        url('/politica-de-cookies'),
    ];

    $posts = News::all();
    foreach ($posts as $post) {
        $urls[] = route('blog.show', $post->slug);
    }

    $xml = view('sitemap', compact('urls'));
    return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
});

Route::get('/robots.txt', function () {
    $content = "User-agent: *\n";
    $content .= "Allow: /\n";
    $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

    return response($content, 200)
        ->header('Content-Type', 'text/plain');
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

Route::view('/politica-de-privacidade', 'pages.politica-de-privacidade')->name('politica-de-privacidade');
Route::view('/termos-de-uso', 'pages.termos-de-uso')->name('termos-de-uso');
Route::view('/politica-de-cookies', 'pages.cookies')->name('cookies');

Route::get('/cdn/{path}', [\App\Http\Controllers\FileProxyController::class, 'servePublic'])
    ->where('path', '.*');

Route::get('/api/home-news', function () {
    $news = \App\Models\News::latest()->take(3)->get()->map(function ($n) {
        return [
            'title' => $n->title,
            'slug' => $n->slug,
            'excerpt' => $n->excerpt,
            'image_url' => $n->image_url,
            'image_webp_url' => $n->image_webp_url,
        ];
    });

    return response()->json($news)
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
})->name('home.news');

/*
|--------------------------------------------------------------------------
| Área de Assinaturas
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/assinaturas', [AssinaturaController::class, 'index'])->name('assinaturas.index');
    Route::post('/checkout', [AssinaturaController::class, 'checkout'])->name('assinatura.checkout');
    Route::view('/assinatura/sucesso', 'assinatura.sucesso')->name('assinaturas.sucesso');
    Route::view('/assinatura/cancelado', 'assinatura.cancelado')->name('assinaturas.cancelado');
    Route::get('/minha-assinatura', [AssinaturaController::class, 'minha'])->name('assinaturas.minha');
    Route::post('/cancelar-assinatura', [AssinaturaController::class, 'cancelar'])->name('assinatura.cancelar');
    Route::post('/assinatura/reativar', [AssinaturaController::class, 'reativar'])->name('assinatura.reativar');
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
    Route::patch('/slug', [ProfileController::class, 'updateSlug'])->name('update.slug');
    Route::patch('/landing', [ProfileController::class, 'updateLanding'])->name('update.landing');
});

/*
|--------------------------------------------------------------------------
| Área autenticada com assinatura ativa
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', CheckSubscription::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/sessoes-json', [SessaoController::class, 'storeJson'])->name('sessoes.store.json');
    Route::put('/sessoes-json/{id}', [SessaoController::class, 'updateJson'])->name('sessoes.update.json');
    Route::get('/sessoes-json/{id}/edit', [SessaoController::class, 'editJson'])->name('sessoes.editJson');
    Route::get('/sessoes-json/{id}', [SessaoController::class, 'showJson'])->name('sessoes.showJson');
    Route::delete('/sessoes-json/{id}', [SessaoController::class, 'destroyJson'])->name('sessoes.destroyJson');

    Route::resource('pacientes', PacienteController::class);
    Route::resource('evolucoes', EvolucaoController::class)->parameters(['evolucoes' => 'evolucao']);

    Route::post('/evolucoes/gerar-ia', [ClinicalCopilotController::class, 'gerar'])
        ->name('evolucoes.gerarIA');

    Route::get('/dashboard/pdf', [DashboardController::class, 'exportarPdf'])->name('dashboard.pdf');
    Route::get('/dashboard/excel', [DashboardController::class, 'exportarExcel'])->name('dashboard.excel');

    Route::get('/pacientes/{paciente}/historico', [PacienteController::class, 'historico'])->name('pacientes.historico');
    Route::get('/pacientes/{paciente}/historico/pdf', [PacienteController::class, 'exportarHistoricoPdf'])->name('pacientes.historico.pdf');
    Route::get('/pacientes/{paciente}/indicadores', [PacienteController::class, 'indicadores'])->name('pacientes.indicadores');
    Route::get('/pacientes/{id}/sessoes', [EvolucaoController::class, 'getSessoes'])->name('pacientes.sessoes');

    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda');
    Route::get('/api/sessoes', [AgendaController::class, 'eventos'])->name('agenda.eventos');

    Route::prefix('pacientes/{paciente}/arquivos')->group(function () {
        Route::get('/', [ArquivoController::class, 'index'])->name('arquivos.index');
        Route::post('/', [ArquivoController::class, 'store'])->name('arquivos.store');
    });

    Route::delete('/arquivos/{arquivo}', [ArquivoController::class, 'destroy'])->name('arquivos.destroy');
    Route::put('/arquivos/{arquivo}/renomear', [ArquivoController::class, 'renomear'])->name('arquivos.rename');

    Route::get('/evolucoes/{evolucao}/imprimir', [\App\Http\Controllers\EvolucaoController::class, 'imprimir'])
        ->name('evolucoes.print');

    /*
    |--------------------------------------------------------------------------
    | Área admin
    |--------------------------------------------------------------------------
    */
    Route::middleware([EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/auditoria', [AuditController::class, 'index'])->name('auditoria.index');
        Route::get('/auditoria/exportar-pdf', [AuditController::class, 'exportarPdf'])->name('auditoria.exportar.pdf');
        Route::get('/auditoria/exportar-excel', [AuditController::class, 'exportarExcel'])->name('auditoria.exportar.excel');

        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::patch('/usuarios/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('usuarios.toggleAdmin');

        Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);

        /*
        |--------------------------------------------------------------------------
        | Simulador admin do webhook WhatsApp
        |--------------------------------------------------------------------------
        */
        Route::get('/whatsapp/simular', [WebhookWhatsappController::class, 'formSimulacaoAdmin'])
            ->name('whatsapp.simular.form');

        Route::post('/whatsapp/simular', [WebhookWhatsappController::class, 'simularMensagemAdmin'])
            ->name('whatsapp.simular.enviar');
    });

    /*
    |--------------------------------------------------------------------------
    | Sessões (HTML)
    |--------------------------------------------------------------------------
    */
    Route::get('/sessoes/export', [SessaoController::class, 'export'])->name('sessoes.export');
    Route::post('/sessoes/gerar-recorrencias', [SessaoController::class, 'gerarRecorrencias'])->name('sessoes.gerarRecorrencias');
    Route::resource('sessoes', SessaoController::class)->except(['show']);
    Route::get('/sessoes/importar', [SessaoController::class, 'importarView'])->name('sessoes.importar.view');
    Route::post('/sessoes/importar', [SessaoController::class, 'importar'])->name('sessoes.importar');
    Route::get('/sessoes/modelo', [SessaoController::class, 'baixarModeloImportacao'])->name('sessoes.modelo');
    Route::post('/sessoes/sync/futuras', [SessaoController::class, 'syncFuturas'])->name('sessoes.sync.futuras');
    Route::post('/sessoes/sync/todas', [SessaoController::class, 'syncTodas'])->name('sessoes.sync.todas');

    /*
    |--------------------------------------------------------------------------
    | Notificações
    |--------------------------------------------------------------------------
    */
    Route::get('/notificacoes', [NotificacaoController::class, 'dropdown'])->name('notificacoes.dropdown');
    Route::get('/notificacoes/{id}/acao', [NotificacaoController::class, 'acao'])->name('notificacoes.acao');
    Route::post('/notificacoes/ler-todas', [NotificacaoController::class, 'marcarTodasComoLidas'])->name('notificacoes.ler.todas');

    /*
    |--------------------------------------------------------------------------
    | API auxiliar
    |--------------------------------------------------------------------------
    */
    Route::get('/api/aniversariantes-hoje', [PacienteController::class, 'aniversariantesHoje'])->name('api.aniversariantes');
});

/*
|--------------------------------------------------------------------------
| Integração Google + jobs de sync por usuário
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/integracoes/google/connect', [\App\Http\Controllers\GoogleAuthController::class, 'redirect'])->name('google.connect');
    Route::get('/oauth/google/callback', [\App\Http\Controllers\GoogleAuthController::class, 'callback'])->name('google.callback');
    Route::post('/integracoes/google/disconnect', [\App\Http\Controllers\GoogleAuthController::class, 'disconnect'])->name('google.disconnect');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/billing/portal', [\App\Http\Controllers\AssinaturaController::class, 'portal'])->name('billing.portal');
});

/*
|--------------------------------------------------------------------------
| Stripe Webhook
|--------------------------------------------------------------------------
*/
Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

Route::get('/ver-log-whatsapp', function () {
    $hoje = now()->format('Y-m-d');
    $logFile = storage_path("logs/whatsapp-{$hoje}.log");
    $logContent = file_exists($logFile) ? file_get_contents($logFile) : 'Nenhum log encontrado.';
    return "<pre style='background:#111;color:#0f0;padding:20px;white-space:pre-wrap;'>" . e($logContent) . "</pre>";
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Landing Pages - deve ficar por último
|--------------------------------------------------------------------------
*/
Route::get('/{slug}', [LandingPageController::class, 'show'])
    ->name('landing.show')
    ->where('slug', '[A-Za-z0-9\-]+');

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

// Route::get('/fix-perms', function() {
//     exec('chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache');
//     return 'Permissões corrigidas!';
// });

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

// Route::get('/testar-log-whatsapp', function () {
//     Log::channel('whatsapp')->info('✅ Teste direto no canal whatsapp às ' . now());

//     return 'Log de teste enviado!';
// });

// Route::get('/_filesystem', function () {
//     $path = base_path();
//     $files = [];

//     $iterator = new RecursiveIteratorIterator(
//         new RecursiveCallbackFilterIterator(
//             new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
//             function ($current, $key, $iterator) {
//                 $pathname = $current->getPathname();
//                 if (
//                     strpos($pathname, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false ||
//                     strpos($pathname, DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR) !== false ||
//                     strpos($pathname, DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'lost+found') !== false ||
//                     strpos($current->getFilename(), '.') === 0
//                 ) {
//                     return false;
//                 }

//                 if ($current->isDir() && !is_readable($pathname)) {
//                     return false;
//                 }

//                 return true;
//             }
//         ),
//         RecursiveIteratorIterator::SELF_FIRST
//     );

//     foreach ($iterator as $file) {
//         if ($file->isFile()) {
//             try {
//                 $fullPath = $file->getPathname();
//                 $relativePath = str_replace($path, '', $fullPath);

//                 $files[] = [
//                     'path' => $relativePath,
//                     'last_modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
//                     'size' => $file->getSize(),
//                 ];
//             } catch (Exception $e) {
//                 continue;
//             }
//         }
//     }

//     return view('filesystem', ['files' => $files]);
// })->name('filesystem.index');

// Route::get('/_filesystem/view', function (\Illuminate\Http\Request $request) {
//     $file = $request->query('file');
//     if (!$file) {
//         abort(404, 'Arquivo não especificado.');
//     }

//     $fullPath = base_path($file);

//     if (!file_exists($fullPath)) {
//         abort(404, 'Arquivo não encontrado.');
//     }

//     return Response::file($fullPath);
// })->name('filesystem.view');

// Route::get('/_filesystem/download', function (\Illuminate\Http\Request $request) {
//     $file = $request->query('file');
//     if (!$file) {
//         abort(404, 'Arquivo não especificado.');
//     }

//     $fullPath = base_path($file);

//     if (!file_exists($fullPath)) {
//         abort(404, 'Arquivo não encontrado.');
//     }

//     return Response::download($fullPath);
// })->name('filesystem.download');

// Route::get('/run-migrate', function (\Illuminate\Http\Request $request) {
//     if ($request->query('token') !== env('MIGRATE_TOKEN')) {
//         abort(403, 'Token inválido.');
//     }
//     Artisan::call('migrate', ['--force' => true]);
//     return Response::make('Migração executada com sucesso!', 200);
// });
