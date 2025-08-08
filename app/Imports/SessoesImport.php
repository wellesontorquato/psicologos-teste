<?php

namespace App\Imports;

use App\Models\Sessao;
use App\Models\Evolucao;
use App\Models\Paciente;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class SessoesImport implements ToCollection
{
    protected $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function collection(Collection $rows)
    {
        $agora = now();
        $sucesso = 0;

        Log::channel('whatsapp')->info("📥 Iniciando importação de sessões (Total: {$rows->count()} linhas)");

        foreach ($rows->skip(1) as $index => $linha) {
            $linhaNum = $index + 2;

            if (count($linha) < 7) {
                Log::channel('whatsapp')->warning("⚠️ Linha {$linhaNum}: incompleta (menos de 7 colunas). Dados: " . json_encode($linha));
                continue;
            }

            [$nomePaciente, $dataHoraBr, $duracaoMin, $valorBr, $pago, $status, $textoEvolucao] = $linha;

            if (!$nomePaciente || !$dataHoraBr) {
                Log::channel('whatsapp')->warning("⚠️ Linha {$linhaNum}: nome ou data ausente.");
                continue;
            }

            $nomePacienteLimpo = trim(preg_replace('/\s+/', ' ', $nomePaciente));

            $paciente = Paciente::whereRaw('LOWER(nome) = ?', [strtolower($nomePacienteLimpo)])
                ->where('user_id', $this->user_id)
                ->first();

            if (!$paciente) {
                Log::channel('whatsapp')->error("❌ Linha {$linhaNum}: paciente '{$nomePacienteLimpo}' não encontrado.");
                continue;
            }

            try {
                $dataHora = Carbon::createFromFormat('d/m/Y H:i', trim($dataHoraBr));
            } catch (\Exception $e) {
                Log::channel('whatsapp')->error("❌ Linha {$linhaNum}: data inválida '{$dataHoraBr}'.");
                continue;
            }

            $valor = floatval(str_replace(',', '.', str_replace('.', '', $valorBr)));

            $sessao = Sessao::create([
                'user_id' => $this->user_id,
                'paciente_id' => $paciente->id,
                'data_hora' => $dataHora,
                'data_hora_original' => $dataHora,
                'duracao' => intval($duracaoMin),
                'valor' => $valor,
                'foi_pago' => strtolower(trim($pago)) === 'sim' ? 1 : 0,
                'status_confirmacao' => ucfirst(strtolower(trim($status))),
                'lembrete_enviado' => $dataHora < $agora ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::channel('whatsapp')->info("✅ Linha {$linhaNum}: sessão criada para {$paciente->nome} em {$dataHora->format('d/m/Y H:i')}");

            if ($dataHora < $agora && !empty(trim($textoEvolucao))) {
                Evolucao::create([
                    'paciente_id' => $paciente->id,
                    'sessao_id' => $sessao->id,
                    'data' => $dataHora,
                    'texto' => $textoEvolucao,
                    'tipo' => '',
                ]);
                Log::channel('whatsapp')->info("📝 Linha {$linhaNum}: evolução registrada para {$paciente->nome}");
            }

            $sucesso++;
        }

        Log::channel('whatsapp')->info("🏁 Importação concluída. Total de sessões criadas: {$sucesso}");
    }
}
