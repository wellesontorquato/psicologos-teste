<?php

namespace App\Exports\Sheets;

use App\Models\Paciente;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PacientesSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize
{
    protected $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

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
        return Paciente::where('user_id', $this->user_id)
            ->orderBy('nome')
            ->pluck('nome')
            ->map(fn ($nome) => [$nome])
            ->toArray();
    }
}
