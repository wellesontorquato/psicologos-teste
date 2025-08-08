<?php

namespace App\Imports;

use App\Models\Sessao;
use App\Models\Evolucao;
use App\Models\Paciente;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
        $totalLinhas = $rows->count() - 1; // Desconta cabeçalho
        $maxLinhas = min(50, $totalLinhas);

        Log::channel('whatsapp')->info("📥 Iniciando importação de sessões (Total: {$totalLinhas} linhas)");

        foreach ($rows->skip(1)->take($maxLinhas) as $index => $linha) {
            $linhaNum = $index + 2;

            if (count($linha) < 7) {
                $msg = "⚠️ Linha {$linhaNum}: incompleta (menos de 7 colunas).";
                $this->mensagens[] = $msg;
                Log::channel('whatsapp')->warning($msg);
                continue;
            }

            [$nomePaciente, $dataHoraBr, $duracaoMin, $valorBr, $pago, $status, $textoEvolucao] = $linha;

            if (!$nomePaciente || !$dataHoraBr) {
                $msg = "⚠️ Linha {$linhaNum}: nome do paciente ou data ausente.";
                $this->mensagens[] = $msg;
                Log::channel('whatsapp')->warning($msg);
                continue;
            }

            $nomePacienteLimpo = trim(preg_replace('/\s+/', ' ', $nomePaciente));

            $paciente = Paciente::whereRaw('LOWER(nome) = ?', [strtolower($nomePacienteLimpo)])
                ->where('user_id', $this->user_id)
                ->first();

            if (!$paciente) {
                $msg = "❌ Linha {$linhaNum}: paciente '{$nomePacienteLimpo}' não encontrado.";
                $this->mensagens[] = $msg;
                Log::channel('whatsapp')->error($msg);
                continue;
            }

            try {
                if (is_numeric($dataHoraBr)) {
                    $dataHora = Carbon::instance(Date::excelToDateTimeObject($dataHoraBr));
                } else {
                    $dataHora = Carbon::createFromFormat('d/m/Y H:i', trim($dataHoraBr));
                }
            } catch (\Exception $e) {
                $msg = "❌ Linha {$linhaNum}: data inválida '{$dataHoraBr}'.";
                $this->mensagens[] = $msg;
                Log::channel('whatsapp')->error($msg);
                continue;
            }

            // VERIFICA SE SESSÃO JÁ EXISTE
            $jaExiste = Sessao::where('user_id', $this->user_id)
                ->where('paciente_id', $paciente->id)
                ->where('data_hora', $dataHora)
                ->exists();

            if ($jaExiste) {
                $msg = "🔁 Linha {$linhaNum}: sessão já existente para {$paciente->nome} em {$dataHora->format('d/m/Y H:i')}. Ignorada.";
                $this->mensagens[] = $msg;
                Log::channel('whatsapp')->info($msg);
                continue;
            }

            $valor = floatval(str_replace(',', '.', str_replace('.', '', $valorBr)));

            // CAMPOS AUTOMÁTICOS
            $lembreteEnviado = $dataHora < $agora ? 1 : 0;
            $statusConfirmacao = $dataHora < $agora ? 'Confirmada' : 'Pendente';

            $sessao = Sessao::create([
                'user_id' => $this->user_id,
                'paciente_id' => $paciente->id,
                'data_hora' => $dataHora,
                'data_hora_original' => $dataHora,
                'duracao' => intval($duracaoMin),
                'valor' => $valor,
                'foi_pago' => strtolower(trim($pago)) === 'sim' ? 1 : 0,
                'status_confirmacao' => $statusConfirmacao,
                'lembrete_enviado' => $lembreteEnviado,
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

        $mensagemFinal = "✅ Importação finalizada: {$sucesso} sessão(ões) criada(s).";
        $this->mensagens[] = $mensagemFinal;

        if ($totalLinhas > $maxLinhas) {
            $this->mensagens[] = "⚠️ Apenas as primeiras 50 linhas foram processadas.";
        }

        session()->flash('resultado_importacao', $this->mensagens);

        Log::channel('whatsapp')->info("🏁 Importação concluída. Total de sessões criadas: {$sucesso}");
    }
}
