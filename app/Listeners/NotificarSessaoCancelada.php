<?php

namespace App\Listeners;

use App\Events\SessaoCancelada;
use App\Models\Notificacao;
use App\Models\Sessao;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificarSessaoCancelada
{
    public function handle(SessaoCancelada $event)
    {
        $sessao = $event->sessao;
        $sessao->loadMissing('paciente');

        if (!$sessao->paciente) {
            Log::warning('[Notificacao] ❌ Sessão cancelada sem paciente carregado.', ['sessao_id' => $sessao->id]);
            return;
        }

        $existe = Notificacao::where('user_id', $sessao->paciente->user_id)
            ->where('relacionado_type', Sessao::class)
            ->where('relacionado_id', $sessao->id)
            ->where('tipo', 'whatsapp_cancelada')
            ->exists();

        if ($existe) {
            Log::info('[Notificacao] ⚠️ Notificação de cancelamento já existe.', ['sessao_id' => $sessao->id]);
            return;
        }

        // ✅ MONTAGEM DA MENSAGEM COMPLETA COM DATA ORIGINAL
        $pacienteNome = $sessao->paciente->nome ?? 'Paciente desconhecido';
        $dataOriginal = $sessao->data_hora_original
            ? Carbon::parse($sessao->data_hora_original)->format('d/m/Y \à\s H:i')
            : 'Data original não disponível';

        $mensagem = "O paciente cancelou a sessão que estava marcada para {$dataOriginal}.";

        Notificacao::create([
            'user_id' => $sessao->paciente->user_id,
            'titulo' => 'Sessão cancelada',
            'mensagem' => $mensagem,
            'tipo' => 'whatsapp_cancelada',
            'relacionado_id' => $sessao->id,
            'relacionado_type' => Sessao::class,
        ]);

        Log::info('[Notificacao] ✅ Notificação de cancelamento criada.', [
            'sessao_id' => $sessao->id,
            'mensagem' => $mensagem,
        ]);
    }
}
