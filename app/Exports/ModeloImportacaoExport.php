<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Paciente;

class ModeloImportacaoExport implements FromView
{
    protected $pacientes;

    public function __construct($pacientes)
    {
        $this->pacientes = $pacientes;
    }

    public function view(): View
    {
        return view('exports.modelo-importacao', [
            'pacientes' => $this->pacientes,
        ]);
    }
}
