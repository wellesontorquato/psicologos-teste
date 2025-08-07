<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ModeloSessoesSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Modelo de Sessões';
    }

    public function headings(): array
    {
        return [
            'Paciente',
            'Data',
            'Duração (Minutos)',
            'Valor',
            'Pago',
            'Status',
            'Evolução'
        ];
    }

    public function array(): array
    {
        return [
            ['João da Silva', '31/07/2025 10:00', '50', '150,00', 'Sim', 'Confirmada', 'Texto de exemplo']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '007BFF']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']]
            ]
        ];
    }
}
