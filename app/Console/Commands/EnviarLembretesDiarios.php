<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\LembreteController;

class EnviarLembretesDiarios extends Command
{
    protected $signature = 'lembretes:enviar';
    protected $description = 'Envia lembretes automáticos de sessões do dia seguinte via WhatsApp.';

    public function handle(): void
    {
        $controller = new LembreteController();
        $resposta = $controller->enviarLembretesManualmente();

        $this->info('Envio de lembretes agendado:');
        dump($resposta->getContent());
    }
}
