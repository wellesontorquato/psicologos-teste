<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\LembreteController;
use Illuminate\Support\Facades\Log;

class EnviarLembretesDiarios extends Command
{
    protected $signature = 'lembretes:enviar';
    protected $description = 'Envia lembretes automáticos de sessões do dia seguinte via WhatsApp.';

    public function handle(): void
    {
        Log::info('[Lembretes] 📅 Executando lembretes:enviar (comando agendado iniciado)');

        $controller = new LembreteController();
        $resposta = $controller->enviarLembretesManualmente();

        $this->info('Envio de lembretes agendado:');
        dump($resposta->getContent());

        Log::info('[Lembretes] ✅ Lembretes enviados com sucesso.', [
            'resposta' => $resposta->getContent(),
        ]);
    }
}
