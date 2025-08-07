<?php

namespace App\Exports;

use App\Exports\Sheets\ModeloSessoesSheet;
use App\Exports\Sheets\PacientesSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ModeloImportacaoSessoesExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ModeloSessoesSheet(),
            new PacientesSheet(),
        ];
    }
}
