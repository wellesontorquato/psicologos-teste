<?php

namespace App\Services\ReceitaSaude;

use App\Models\ReceitaSaudeRecibo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceitaSaudeCsvService
{
    public const CODIGO_RENDIMENTO_RECIBO = 'R01.001.001';

    public const OCUPACOES = [
        'psiquiatra' => '225', // Médico
        'psicologo' => '255',  // Psicólogo
    ];

    public function codigoOcupacaoParaUsuario(User $user): ?string
    {
        $tipo = Str::of((string) $user->tipo_profissional)->lower()->trim()->toString();

        return self::OCUPACOES[$tipo] ?? null;
    }

    public function cpfSomenteNumeros(?string $cpf): string
    {
        return preg_replace('/\D/', '', (string) $cpf) ?? '';
    }

    public function registroProfissional(?string $registro): string
    {
        $registro = trim((string) $registro);
        $registro = preg_replace('/[^A-Za-z0-9\/\-\.]/', '', $registro) ?? '';

        return mb_substr($registro, 0, 15);
    }

    public function descricaoPadrao(ReceitaSaudeRecibo $recibo): string
    {
        $data = $recibo->data_atendimento?->format('d/m/Y');

        $descricao = $data
            ? "Atendimento psicológico em {$data}"
            : 'Atendimento psicológico';

        return mb_substr($descricao, 0, 255);
    }

    /**
     * @param Collection<int, ReceitaSaudeRecibo> $recibos
     */
    public function validarParaExportacao(Collection $recibos): void
    {
        if ($recibos->isEmpty()) {
            throw ValidationException::withMessages([
                'recibos' => 'Selecione pelo menos um recibo para exportar.',
            ]);
        }

        if ($recibos->count() > 1000) {
            throw ValidationException::withMessages([
                'recibos' => 'O arquivo do Receita Saúde aceita no máximo 1000 linhas por importação.',
            ]);
        }

        $anos = $recibos
            ->map(fn (ReceitaSaudeRecibo $recibo) => optional($recibo->data_pagamento)->format('Y'))
            ->filter()
            ->unique()
            ->values();

        if ($anos->count() > 1) {
            throw ValidationException::withMessages([
                'recibos' => 'Todos os pagamentos do arquivo precisam ser do mesmo ano-calendário.',
            ]);
        }

        $erros = [];

        foreach ($recibos as $recibo) {
            $linha = "Recibo #{$recibo->id}";

            if (!$recibo->data_pagamento) {
                $erros[] = "{$linha}: informe a data do pagamento.";
            }

            if (!in_array($recibo->codigo_ocupacao, ['225', '226', '230', '231', '232', '255'], true)) {
                $erros[] = "{$linha}: código de ocupação inválido.";
            }

            if ((float) $recibo->valor_pagamento <= 0) {
                $erros[] = "{$linha}: o valor do pagamento precisa ser maior que zero.";
            }

            foreach ([
                'CPF do pagador' => $recibo->cpf_pagador,
                'CPF do beneficiário' => $recibo->cpf_beneficiario,
                'CPF do profissional' => $recibo->cpf_profissional,
            ] as $campo => $cpf) {
                if (strlen($this->cpfSomenteNumeros($cpf)) !== 11) {
                    $erros[] = "{$linha}: {$campo} precisa ter 11 números.";
                }
            }

            if (mb_strlen((string) $recibo->descricao) > 255) {
                $erros[] = "{$linha}: a descrição deve ter no máximo 255 caracteres.";
            }
        }

        if ($erros) {
            throw ValidationException::withMessages([
                'recibos' => implode("\n", $erros),
            ]);
        }
    }

    /**
     * @param Collection<int, ReceitaSaudeRecibo> $recibos
     */
    public function downloadCsv(Collection $recibos): StreamedResponse
    {
        $this->validarParaExportacao($recibos);

        $filename = 'receita_saude_' . now('America/Sao_Paulo')->format('Ymd_His') . '.csv';

        $recibos->each(function (ReceitaSaudeRecibo $recibo) {
            $recibo->update([
                'status' => 'exportado',
                'exportado_em' => now(),
            ]);
        });

        return response()->streamDownload(function () use ($recibos) {
            $handle = fopen('php://output', 'w');

            foreach ($recibos as $recibo) {
                fputcsv($handle, $this->linhaCsv($recibo), ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function linhaCsv(ReceitaSaudeRecibo $recibo): array
    {
        return [
            $recibo->data_pagamento->format('d/m/Y'),
            self::CODIGO_RENDIMENTO_RECIBO,
            $recibo->codigo_ocupacao,
            number_format((float) $recibo->valor_pagamento, 2, ',', ''),
            '',
            mb_substr((string) $recibo->descricao, 0, 255),
            'PF',
            $this->cpfSomenteNumeros($recibo->cpf_pagador),
            $this->cpfSomenteNumeros($recibo->cpf_beneficiario),
            '',
            '',
            '',
            '',
            'S',
            $this->cpfSomenteNumeros($recibo->cpf_profissional),
            $this->registroProfissional($recibo->registro_profissional),
        ];
    }
}
