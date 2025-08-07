<?php

namespace App\Listeners;

use App\Events\PacienteAniversariante;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificarAniversarioPaciente
{
    public function handle(PacienteAniversariante $event)
    {
        $paciente = $event->paciente;

        $hoje = Carbon::now()->toDateString(); // só a data Y-m-d

        $notificacao = Notificacao::where('user_id', $paciente->user_id)
            ->where('tipo', 'aniversario')
            ->where('relacionado_id', $paciente->id)
            ->where('relacionado_type', \App\Models\Paciente::class)
            ->whereDate('created_at', $hoje)
            ->first();

        if ($notificacao) {
            Log::info("[Aniversário] ⛔ Notificação já existente hoje para paciente {$paciente->nome}");
            return;
        }

        Notificacao::create([
            'user_id' => $paciente->user_id,
            'titulo' => 'Aniversário do paciente hoje',
            'mensagem' => "Hoje é aniversário de {$paciente->nome} 🎂",
            'tipo' => 'aniversario',
            'relacionado_id' => $paciente->id,
            'relacionado_type' => \App\Models\Paciente::class,
            'lida' => false,
        ]);

        Log::info("[Aniversário] 🎉 Notificação criada para paciente {$paciente->nome}");
    }
}
