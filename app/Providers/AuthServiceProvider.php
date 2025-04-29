<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Paciente;
use App\Models\Arquivo;
use App\Policies\PacientePolicy;
use App\Policies\ArquivoPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * As policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Paciente::class => PacientePolicy::class,
        Arquivo::class  => ArquivoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ✅ Corrigido para aceitar is_admin como boolean ou inteiro
        Gate::define('view-auditoria', function ($user) {
            \Log::debug('🧠 Executando Gate view-auditoria', [
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'is_admin' => $user->is_admin ?? null,
            ]);
        
            return (bool) $user->is_admin;
        });        
    }
}
