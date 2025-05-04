<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\VerificarSessoesNaoPagas;
use App\Console\Commands\ChecarAniversariantes;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Executa lembretes às 8h
        $schedule->command('lembretes:enviar')->everyMinute()->runInBackground();
        Log::info('[Lembretes] Executando lembretes:enviar com sucesso');

        // Verifica sessões não pagas às 7h
        $schedule->command('checar:sessoes-nao-pagas')->everyMinute()->runInBackground();
        Log::info('[Lembretes] checar:sessoes-nao-pagas com sucesso');


        // Verifica aniversariantes às 7h30
        $schedule->command('checar:aniversariantes')->everyMinute()->runInBackground();
        Log::info('[Lembretes] checar:aniversariantes com sucesso');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }

    protected $commands = [
        VerificarSessoesNaoPagas::class,
        ChecarAniversariantes::class,
    ];
}
