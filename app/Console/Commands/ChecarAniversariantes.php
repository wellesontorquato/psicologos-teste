<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Paciente;
use App\Events\PacienteAniversariante;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ChecarAniversariantes extends Command
{
    protected $signature = 'checar:aniversariantes';

    protected $description = 'Dispara evento para pacientes aniversariantes do dia';

    public function handle(): int
    {
        $hoje = Carbon::now();
        $pacientes = Paciente::whereDay('data_nascimento', $hoje->day)
                             ->whereMonth('data_nascimento', $hoje->month)
                             ->get();

        if ($pacientes->isEmpty()) {
            Log::info('[Aniversário] Nenhum paciente aniversariante hoje.');
            return Command::SUCCESS;
        }

        foreach ($pacientes as $paciente) {
            event(new PacienteAniversariante($paciente));
        }

        $this->info("🎉 " . count($pacientes) . " paciente(s) aniversariantes hoje.");
        return Command::SUCCESS;
    }
}
