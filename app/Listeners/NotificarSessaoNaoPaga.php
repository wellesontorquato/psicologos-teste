<?php

namespace App\Listeners;

use App\Events\SessaoNaoPaga;
use App\Models\Notificacao;
use App\Models\Sessao;
use Illuminate\Support\Facades\Log;

class NotificarSessaoNaoPaga
{
    public function handle(SessaoNaoPaga $event)
    {
        $sessao = $event->sessao;

        $sessao->loadMissing('paciente');

        if (!$sessao->paciente) {
            Log::warning('[Notificacao] ❌ Sessão não paga sem paciente carregado.', ['sessao_id' => $sessao->id]);
            return;
        }

        // ✅ Verifica se já existe notificação para esta sessão
        $existe = Notificacao::where('user_id', $sessao->paciente->user_id)
            ->where('tipo', 'sessao_nao_paga')
            ->where('relacionado_id', $sessao->id)
            ->where('relacionado_type', Sessao::class)
            ->exists();

        if ($existe) {
            Log::info('[Notificacao] ⛔ Notificação já existente. Ignorada.', ['sessao_id' => $sessao->id]);
            return;
        }

        $pacienteNome = $sessao->paciente->nome ?? 'Paciente desconhecido';
        $dataHora = optional($sessao->data_hora)->format('d/m/Y \à\s H:i') ?? 'Data não disponível';

        Notificacao::create([
            'user_id' => $sessao->paciente->user_id,
            'titulo' => 'Sessão ainda não foi paga',
            'mensagem' => "A sessão de *{$pacienteNome}*, realizada em *{$dataHora}*, ainda está com pagamento pendente.",
            'tipo' => 'sessao_nao_paga',
            'relacionado_id' => $sessao->id,
            'relacionado_type' => Sessao::class,
        ]);

        Log::info('[Notificacao] ⚠️ Notificação de sessão não paga criada.', [
            'sessao_id' => $sessao->id,
            'paciente' => $pacienteNome,
            'data_hora' => $dataHora,
        ]);
    }
}
