<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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

        /**
         * Permissão para visualizar auditoria/admin
         * Só permite acesso se o usuário estiver autenticado e for admin
         */
        Gate::define('view-auditoria', function ($user) {
            $isAdmin = (int) ($user->is_admin ?? 0) === 1;

            Log::debug('🧠 Gate view-auditoria chamada', [
                'user_id'     => $user->id ?? null,
                'email'       => $user->email ?? null,
                'is_admin'    => $user->is_admin ?? null,
                'autorizado'  => $isAdmin,
            ]);

            return $isAdmin;
        });
    }
}
