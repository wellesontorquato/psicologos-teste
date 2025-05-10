<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Models\Paciente;
use App\Models\Arquivo;
use App\Policies\PacientePolicy;
use App\Policies\ArquivoPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Mail\CustomVerifyEmail;

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

        // Substitui o e-mail padrão de verificação por um customizado
        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return new \App\Mail\CustomVerifyEmail($url);
        });

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
