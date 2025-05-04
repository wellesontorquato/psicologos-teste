<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log; // IMPORTANTE: adiciona essa linha!
use App\Console\Commands\VerificarSessoesNaoPagas;
use App\Console\Commands\ChecarAniversariantes;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Log para testar se está entrando aqui
        Log::info('[Kernel] Entrou no método schedule() e está carregando os comandos.');

        // Executa lembretes às 8h (para teste, está everyMinute)
        $schedule->command('lembretes:enviar')->everyMinute()->runInBackground();

        // Verifica sessões não pagas às 7h (para teste, está everyMinute)
        $schedule->command('checar:sessoes-nao-pagas')->everyMinute()->runInBackground();

        // Verifica aniversariantes às 7h30 (para teste, está everyMinute)
        $schedule->command('checar:aniversariantes')->everyMinute()->runInBackground();
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
