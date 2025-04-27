<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AuditoriaExport;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 🔍 Log detalhado para diagnóstico
        Log::debug('🔍 Verificando acesso à auditoria', [
            'user_id' => $user->id ?? null,
            'email' => $user->email ?? null,
            'is_admin' => $user->is_admin ?? null,
            'gate_allows' => Gate::allows('view-auditoria'),
        ]);

        if (!Gate::allows('view-auditoria')) {
            abort(403, 'Acesso negado à auditoria.');
        }

        $query = $this->filtrarLogs($request);
        $audits = $query->paginate(20);
        $usuarios = User::orderBy('name')->get();

        return view('auditoria.index', compact('audits', 'usuarios'));
    }

    public function exportarPdf(Request $request)
    {
        $logs = $this->filtrarLogs($request)->get();

        $pdf = PDF::loadView('auditoria.exportar_pdf', compact('logs'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('auditoria.pdf');
    }

    public function exportarExcel(Request $request)
    {
        return Excel::download(new AuditoriaExport($request), 'auditoria.xlsx');
    }

    private function filtrarLogs(Request $request)
    {
        $query = Audit::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('de')) {
            $query->whereDate('created_at', '>=', $request->de);
        }

        if ($request->filled('ate')) {
            $query->whereDate('created_at', '<=', $request->ate);
        }

        return $query;
    }
}
