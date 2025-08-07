<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Audit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AuditoriaExport implements FromCollection, WithHeadings
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $query = Audit::with('user')->latest();

        if ($this->request->filled('user_id')) {
            $query->where('user_id', $this->request->user_id);
        }

        if ($this->request->filled('action')) {
            $query->where('action', 'like', '%' . $this->request->action . '%');
        }

        if ($this->request->filled('de')) {
            $query->whereDate('created_at', '>=', $this->request->de);
        }

        if ($this->request->filled('ate')) {
            $query->whereDate('created_at', '<=', $this->request->ate);
        }

        return $query->get()->map(function ($log) {
            return [
                $log->user->name ?? 'Desconhecido',
                $log->action,
                $log->description,
                $log->ip_address,
                $log->created_at->format('d/m/Y H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Usuário',
            'Ação',
            'Descrição',
            'IP',
            'Data/Hora',
        ];
    }
}
