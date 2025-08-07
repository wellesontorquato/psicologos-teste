<?php

namespace App\Exports;

use App\Exports\Sheets\ModeloSessoesSheet;
use App\Exports\Sheets\PacientesSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ModeloImportacaoSessoesExport implements WithMultipleSheets
{
    protected $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function sheets(): array
    {
        return [
            new ModeloSessoesSheet(),
            new PacientesSheet($this->user_id),
        ];
    }
}
