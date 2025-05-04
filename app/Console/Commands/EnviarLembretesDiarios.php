<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Console\Commands\VerificarSessoesNaoPagas;
use App\Console\Commands\ChecarAniversariantes;
use App\Console\Commands\EnviarLembretesDiarios; // ✅ garantir que está registrado

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        Log::info('[Kernel] ✅ Entrou no método schedule() e está configurando os comandos.');

        $schedule->command('lembretes:enviar')
            ->everyMinute()
            ->runInBackground();

        $schedule->command('checar:sessoes-nao-pagas')
            ->everyMinute()
            ->runInBackground();

        $schedule->command('checar:aniversariantes')
            ->everyMinute()
            ->runInBackground();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }

    protected $commands = [
        VerificarSessoesNaoPagas::class,
        ChecarAniversariantes::class,
        EnviarLembretesDiarios::class, // ✅ garantir que tá aqui também
    ];
}
