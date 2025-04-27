<?php

namespace App\Listeners;

use App\Events\PacienteAniversariante;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Log;

class NotificarAniversarioPaciente
{
    public function handle(PacienteAniversariante $event)
    {
        $paciente = $event->paciente;

        Notificacao::updateOrCreate(
            [
                'user_id' => $paciente->user_id,
                'tipo' => 'aniversario',
                'relacionado_id' => $paciente->id,
                'relacionado_type' => \App\Models\Paciente::class,
                'titulo' => 'Aniversário do paciente hoje',
            ],
            [
                'mensagem' => "Hoje é aniversário de {$paciente->nome} 🎂",
                'lida' => false,
            ]
        );

        Log::info("[Aniversário] 🎉 Notificação criada para paciente {$paciente->nome}");
    }
}
