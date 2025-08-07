<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

// Importando eventos e listeners personalizados
use App\Events\SessaoConfirmada;
use App\Events\SessaoCancelada;
use App\Events\SessaoRemarcada;
use App\Events\SessaoNaoPaga;
use App\Events\PacienteAniversariante;

use App\Listeners\NotificarSessaoConfirmada;
use App\Listeners\NotificarSessaoCancelada;
use App\Listeners\NotificarSessaoRemarcada;
use App\Listeners\NotificarSessaoNaoPaga;
use App\Listeners\NotificarAniversarioPaciente;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],

        // Eventos personalizados do sistema PsiGestor
        SessaoConfirmada::class => [
            NotificarSessaoConfirmada::class,
        ],
        SessaoCancelada::class => [
            NotificarSessaoCancelada::class,
        ],
        SessaoRemarcada::class => [
            NotificarSessaoRemarcada::class,
        ],
        SessaoNaoPaga::class => [
            NotificarSessaoNaoPaga::class,
        ],
        PacienteAniversariante::class => [
            NotificarAniversarioPaciente::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
