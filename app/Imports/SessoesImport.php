<?php

namespace App\Imports;

use App\Models\Sessao;
use App\Models\Evolucao;
use App\Models\Paciente;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;

class SessoesImport implements ToCollection
{
    protected $user_id;
    protected $mensagens = [];

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function collection(Collection $rows)
    {
        $agora = now();
        $sucesso = 0;

        foreach ($rows->skip(1) as $index => $linha) {
            $linhaNum = $index + 2; // +2 para contar cabeçalho e linha 1-based

            if (count($linha) < 7) {
                $this->mensagens[] = "⚠️ Linha {$linhaNum}: incompleta (menos de 7 colunas).";
                continue;
            }

            [$nomePaciente, $dataHoraBr, $duracaoMin, $valorBr, $pago, $status, $textoEvolucao] = $linha;

            if (!$nomePaciente || !$dataHoraBr) {
                $this->mensagens[] = "⚠️ Linha {$linhaNum}: nome do paciente ou data ausente.";
                continue;
            }

            $nomePacienteLimpo = trim(preg_replace('/\s+/', ' ', $nomePaciente));

            $paciente = Paciente::whereRaw('LOWER(nome) = ?', [strtolower($nomePacienteLimpo)])
                ->where('user_id', $this->user_id)
                ->first();

            if (!$paciente) {
                $this->mensagens[] = "❌ Linha {$linhaNum}: paciente '{$nomePacienteLimpo}' não encontrado.";
                continue;
            }

            try {
                $dataHora = Carbon::createFromFormat('d/m/Y H:i', trim($dataHoraBr));
            } catch (\Exception $e) {
                $this->mensagens[] = "❌ Linha {$linhaNum}: data inválida '{$dataHoraBr}'.";
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

            if ($dataHora < $agora && !empty(trim($textoEvolucao))) {
                Evolucao::create([
                    'paciente_id' => $paciente->id,
                    'sessao_id' => $sessao->id,
                    'data' => $dataHora,
                    'texto' => $textoEvolucao,
                    'tipo' => '',
                ]);
            }

            $sucesso++;
        }

        $this->mensagens[] = "✅ Importação finalizada: {$sucesso} sessões criadas com sucesso.";

        session()->flash('resultado_importacao', $this->mensagens);
    }
}
