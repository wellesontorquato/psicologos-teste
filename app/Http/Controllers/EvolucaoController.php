<?php

namespace App\Http\Controllers;

use App\Models\Evolucao;
use App\Models\Paciente;
use Illuminate\Http\Request;
use App\Helpers\AuditHelper;

class EvolucaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Evolucao::with('paciente')
            ->whereHas('paciente', fn($q) => $q->where('user_id', auth()->id()));

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('texto', 'like', "%$busca%")
                ->orWhereHas('paciente', fn($sub) => $sub->where('nome', 'like', "%$busca%"));
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('periodo')) {
            $hoje = now()->startOfDay();

            if ($request->periodo === 'hoje') {
                $query->whereDate('data', $hoje);
            } elseif ($request->periodo === 'semana') {
                $query->whereBetween('data', [$hoje->copy()->startOfWeek(), $hoje->copy()->endOfWeek()]);
            } elseif ($request->periodo === 'mes') {
                $query->whereBetween('data', [$hoje->copy()->startOfMonth(), $hoje->copy()->endOfMonth()]);
            }
        }

        $evolucoes = $query->orderBy('data', 'desc')->paginate(10)->withQueryString();

        AuditHelper::log('view_evolucoes', 'Visualizou a lista de evoluções clínicas');

        return view('evolucoes.index', compact('evolucoes'));
    }

    public function create()
    {
        $pacientes = Paciente::where('user_id', auth()->id())->get();
        return view('evolucoes.create', compact('pacientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data' => 'required|date',
            'texto' => 'required|string',
        ]);

        $paciente = Paciente::where('id', $request->paciente_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $evolucao = Evolucao::create($request->only(['paciente_id', 'data', 'texto']));

        AuditHelper::log('created_evolucao', 'Adicionou evolução ao paciente ' . $paciente->nome); // ✅ Log de criação

        return redirect()->route('evolucoes.index')->with('success', 'Evolução registrada!');
    }

    public function edit(Evolucao $evolucao)
    {
        $evolucao = Evolucao::with('paciente')->findOrFail($evolucao->id);

        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        $pacientes = Paciente::where('user_id', auth()->id())->get();

        return view('evolucoes.edit', compact('evolucao', 'pacientes'));
    }

    public function update(Request $request, Evolucao $evolucao)
    {
        $evolucao = Evolucao::with('paciente')->findOrFail($evolucao->id);

        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data' => 'required|date',
            'texto' => 'required|string',
        ]);

        $paciente = Paciente::where('id', $request->paciente_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $evolucao->update($request->only(['paciente_id', 'data', 'texto']));

        AuditHelper::log('updated_evolucao', 'Editou evolução do paciente ' . $paciente->nome); // ✅ Log de edição

        return redirect()->route('evolucoes.index')->with('success', 'Evolução atualizada.');
    }

    public function destroy(Evolucao $evolucao)
    {
        $evolucao = Evolucao::with('paciente')->findOrFail($evolucao->id);

        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        $paciente = $evolucao->paciente->nome ?? 'Desconhecido';

        $evolucao->delete();

        AuditHelper::log('deleted_evolucao', 'Removeu evolução do paciente ' . $paciente); // ✅ Log de exclusão

        return redirect()->route('evolucoes.index')->with('success', 'Evolução removida.');
    }
}
