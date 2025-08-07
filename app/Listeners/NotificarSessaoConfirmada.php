<?php

namespace App\Listeners;

use App\Events\SessaoConfirmada;
use App\Models\Notificacao;
use App\Models\Sessao;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificarSessaoConfirmada
{
    public function handle(SessaoConfirmada $event)
    {
        $sessao = $event->sessao;

        // Garante que a relação com paciente está carregada
        $sessao->loadMissing('paciente');

        if (!$sessao->paciente) {
            Log::warning('[Notificacao] ❌ Sessão confirmada sem paciente carregado.', [
                'sessao_id' => $sessao->id
            ]);
            return;
        }

        // 🛑 Verifica se já existe notificação para essa sessão
        $existe = Notificacao::where('user_id', $sessao->paciente->user_id)
            ->where('relacionado_type', Sessao::class)
            ->where('relacionado_id', $sessao->id)
            ->where('tipo', 'whatsapp_confirmado')
            ->exists();

        if ($existe) {
            Log::info('[Notificacao] ⚠️ Notificação já existente para esta sessão.', [
                'sessao_id' => $sessao->id
            ]);
            return;
        }

        // ✅ Monta mensagem usando data_hora_original
        $dataOriginal = $sessao->data_hora_original
            ? Carbon::parse($sessao->data_hora_original)->format('d/m/Y \à\s H:i')
            : 'Data original não disponível';

        $mensagem = "O paciente confirmou a sessão que estava marcada para {$dataOriginal}.";

        // ✅ Cria a notificação
        Notificacao::create([
            'user_id' => $sessao->paciente->user_id,
            'titulo' => 'Sessão confirmada via WhatsApp',
            'mensagem' => $mensagem,
            'tipo' => 'whatsapp_confirmado',
            'relacionado_id' => $sessao->id,
            'relacionado_type' => Sessao::class,
        ]);

        Log::info('[Notificacao] ✅ Notificação criada com sucesso.', [
            'sessao_id' => $sessao->id,
            'mensagem' => $mensagem,
        ]);
    }
}
