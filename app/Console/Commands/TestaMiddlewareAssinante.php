<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\EnsureSubscribed;
use App\Models\User;

class TestaMiddlewareAssinante extends Command
{
    protected $signature = 'diagnostico:assinante';
    protected $description = 'Testa execução do middleware assinante com um usuário autenticado';

    public function handle()
    {
        $this->info('🔍 Testando middleware assinante...');

        $user = User::first(); // Pega o primeiro usuário como exemplo
        if (!$user) {
            $this->error('❌ Nenhum usuário encontrado no banco de dados.');
            return;
        }

        Auth::login($user);

        $middleware = new EnsureSubscribed();

        $request = Request::create('/teste-middleware', 'GET');
        $request->setUserResolver(fn () => $user);

        try {
            $response = $middleware->handle($request, function () {
                return response('✅ Middleware permitiu o acesso');
            });

            $this->info($response->getContent());
        } catch (\Exception $e) {
            $this->error('❌ Middleware bloqueou: ' . $e->getMessage());
        }
    }
}
