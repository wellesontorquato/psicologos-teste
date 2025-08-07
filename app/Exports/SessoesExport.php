<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SessoesExport implements FromCollection, WithHeadings
{
    protected $sessoes;

    public function __construct($sessoes)
    {
        $this->sessoes = $sessoes;
    }

    public function collection()
    {
        return $this->sessoes->map(function ($s) {
            $status = $s->status_confirmacao ?? 'PENDENTE';

            if ($status === 'REMARCAR' && is_null($s->data_hora)) {
                $statusFormatado = 'Reagendar Consulta';
            } elseif ($status === 'REMARCAR' && !is_null($s->data_hora)) {
                $statusFormatado = 'Remarcado';
            } elseif ($status === 'CANCELADA') {
                $statusFormatado = 'Cancelada';
            } else {
                $statusFormatado = ucfirst(strtolower($status));
            }

            $dataFormatada = is_null($s->data_hora)
                ? ($status === 'REMARCAR' ? 'Reagendar Consulta' : ($status === 'CANCELADA' ? 'Consulta Cancelada' : '—'))
                : \Carbon\Carbon::parse($s->data_hora)->format('d/m/Y H:i');

            return [
                'Paciente' => $s->paciente->nome,
                'Data' => $dataFormatada,
                'Duração' => $s->duracao . ' min',
                'Valor' => number_format($s->valor, 2, ',', '.'),
                'Pago' => $s->foi_pago ? 'Sim' : 'Não',
                'Status' => $statusFormatado,
            ];
        });
    }

    public function headings(): array
    {
        return ['Paciente', 'Data', 'Duração', 'Valor', 'Pago', 'Status'];
    }
}
