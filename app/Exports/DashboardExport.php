<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;

class DashboardExport implements FromView
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $controller = new DashboardController();
        $dados = $controller->obterDadosDashboard($this->request);
        return view('dashboard.relatorio_excel', $dados);
    }
}
