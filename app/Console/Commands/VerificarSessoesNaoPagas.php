<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sessao;
use App\Events\SessaoNaoPaga;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class VerificarSessoesNaoPagas extends Command
{
    protected $signature = 'checar:sessoes-nao-pagas';

    protected $description = 'Dispara evento para sessões passadas não pagas';

    public function handle(): int
    {
        $hoje = Carbon::now()->startOfDay();

        $sessoes = Sessao::with('paciente')
            ->where('foi_pago', false)
            ->where('data_hora', '<', $hoje)
            ->where(function ($query) {
                $query->whereNull('status_confirmacao')
                      ->orWhere('status_confirmacao', '!=', 'CANCELADA');
            })
            ->get();

        if ($sessoes->isEmpty()) {
            Log::info('[NaoPaga] Nenhuma sessão não paga encontrada.');
            return Command::SUCCESS;
        }

        foreach ($sessoes as $sessao) {
            Log::info('[NaoPaga] Disparando evento para sessão ID ' . $sessao->id);
            event(new SessaoNaoPaga($sessao));
        }

        $this->info(count($sessoes) . ' sessão(ões) não pagas notificadas.');
        return Command::SUCCESS;
    }
}
