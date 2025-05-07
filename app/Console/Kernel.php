<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Console\Commands\VerificarSessoesNaoPagas;
use App\Console\Commands\ChecarAniversariantes;
use App\Console\Commands\EnviarLembretesDiarios;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        Log::info('[Kernel] ✅ Entrou no método schedule() e está configurando os comandos.');

        // 🔔 Enviar lembretes todo dia às 08:00 (horário padrão)
        $schedule->command('lembretes:enviar')
            ->dailyAt('08:00')
            ->runInBackground();

        // 💰 Checar sessões não pagas todos os dias às 18:00
        $schedule->command('checar:sessoes-nao-pagas')
            ->dailyAt('18:00')
            ->runInBackground();

        // 🎉 Checar aniversariantes todos os dias às 07:30
        $schedule->command('checar:aniversariantes')
            ->dailyAt('07:30')
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
        EnviarLembretesDiarios::class,
    ];
}
