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

    protected $description = 'Dispara evento para sessões confirmadas que já passaram e não foram pagas';

    public function handle(): int
    {
        $agora = Carbon::now(config('app.timezone'));
        Log::info('[NaoPaga] 🔍 Procurando sessões confirmadas não pagas até:', ['agora' => $agora->toDateTimeString()]);

        $sessoes = Sessao::with('paciente')
            ->where('foi_pago', 0)  // 👈 Aqui garantimos que seja 0 mesmo
            ->where('data_hora', '<=', $agora)
            ->where('status_confirmacao', 'CONFIRMADA')
            ->get();

        Log::info('[NaoPaga] 🗂️ Sessões encontradas:', $sessoes->pluck('id')->toArray());

        if ($sessoes->isEmpty()) {
            Log::info('[NaoPaga] ✅ Nenhuma sessão confirmada e não paga encontrada.');
            return Command::SUCCESS;
        }

        foreach ($sessoes as $sessao) {
            Log::info('[NaoPaga] 🚨 Disparando evento para sessão ID ' . $sessao->id);
            event(new SessaoNaoPaga($sessao));
        }

        $this->info(count($sessoes) . ' sessão(ões) não pagas notificadas.');
        return Command::SUCCESS;
    }
}
