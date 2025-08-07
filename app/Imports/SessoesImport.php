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

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function collection(Collection $rows)
    {
        $agora = now();

        foreach ($rows->skip(1) as $linha) {
            [$nomePaciente, $dataHoraBr, $duracaoMin, $valorBr, $pago, $status, $textoEvolucao] = $linha;

            if (!$nomePaciente || !$dataHoraBr) continue;

            $paciente = \App\Models\Paciente::where('nome', 'like', trim($nomePaciente))->first();
            if (!$paciente) continue;

            try {
                $dataHora = Carbon::createFromFormat('d/m/Y H:i', trim($dataHoraBr));
            } catch (\Exception $e) {
                continue;
            }

            $valor = floatval(str_replace(',', '.', str_replace('.', '', $valorBr)));

            $sessao = Sessao::create([
                'user_id' => $this->user_id,
                'paciente_id' => $paciente->id,
                'data_hora' => $dataHora,
                'duracao' => intval($duracaoMin),
                'valor' => $valor,
                'status' => strtolower($pago) === 'sim' ? 'pago' : 'nao_pago',
                'status_confirmacao' => ucfirst(strtolower($status)),
                'lembrete_enviado' => $dataHora < $agora ? 1 : 0,
                'tipo' => 'Presencial',
            ]);

            if ($dataHora < $agora && !empty($textoEvolucao)) {
                Evolucao::create([
                    'paciente_id' => $paciente->id,
                    'sessao_id' => $sessao->id,
                    'data' => $dataHora,
                    'texto' => $textoEvolucao,
                    'tipo' => 'Padrão',
                ]);
            }
        }
    }
}

