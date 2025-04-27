<?php

namespace App\Events;

use App\Models\Paciente;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PacienteAniversariante
{
    use Dispatchable, SerializesModels;

    public $paciente;

    public function __construct(Paciente $paciente)
    {
        $this->paciente = $paciente;
    }
}
