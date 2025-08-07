<?php

namespace App\Events;

use App\Models\Sessao;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class SessaoConfirmada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Sessao $sessao;

    public function __construct(Sessao $sessao)
    {
        $this->sessao = $sessao;
    }
}
