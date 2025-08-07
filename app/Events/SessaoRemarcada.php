<?php

namespace App\Events;

use App\Models\Sessao;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessaoRemarcada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sessao;

    public function __construct(Sessao $sessao)
    {
        $this->sessao = $sessao;
    }
}
