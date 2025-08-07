<?php

namespace App\Exports\Sheets;

use App\Models\Paciente;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PacientesSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize
{
    public function title(): string
    {
        return 'Pacientes';
    }

    public function headings(): array
    {
        return ['Nome Completo'];
    }

    public function array(): array
    {
        return Paciente::orderBy('nome')->pluck('nome')->map(fn ($nome) => [$nome])->toArray();
    }
}
