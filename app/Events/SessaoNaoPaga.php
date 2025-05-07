<?php

namespace App\Events;

use App\Models\Sessao;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessaoNaoPaga
{
    use Dispatchable, SerializesModels;

    public $sessao;

    public function __construct(Sessao $sessao)
    {
        $this->sessao = $sessao;
    }
}

