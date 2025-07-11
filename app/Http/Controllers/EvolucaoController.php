<?php

namespace App\Http\Controllers;

use App\Models\Evolucao;
use App\Models\Paciente;
use Illuminate\Http\Request;
use App\Helpers\AuditHelper;

class EvolucaoController extends Controller
{
    // 🌐 WEB: Listagem com filtros
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

    // 🌐 WEB: Formulário de criação
    public function create(Request $request)
    {
        $pacientes = Paciente::where('user_id', auth()->id())->get();
        $pacienteSelecionado = $request->paciente_id;

        return view('evolucoes.create', compact('pacientes', 'pacienteSelecionado'));
    }

    // 🌐 WEB: Armazenar evolução
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

        AuditHelper::log('created_evolucao', 'Adicionou evolução ao paciente ' . $paciente->nome);

        return redirect()->route('evolucoes.index')->with('success', 'Evolução registrada!');
    }

    // 🌐 WEB: Formulário de edição
    public function edit(Evolucao $evolucao)
    {
        $evolucao = Evolucao::with('paciente')->findOrFail($evolucao->id);

        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        $pacientes = Paciente::where('user_id', auth()->id())->get();
        return view('evolucoes.edit', compact('evolucao', 'pacientes'));
    }

    // 🌐 WEB: Atualizar evolução
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

        AuditHelper::log('updated_evolucao', 'Editou evolução do paciente ' . $paciente->nome);

        return redirect()->route('evolucoes.index')->with('success', 'Evolução atualizada.');
    }

    // 🌐 WEB: Deletar evolução
    public function destroy(Evolucao $evolucao)
    {
        $evolucao = Evolucao::with('paciente')->findOrFail($evolucao->id);

        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        $paciente = $evolucao->paciente->nome ?? 'Desconhecido';

        $evolucao->delete();

        AuditHelper::log('deleted_evolucao', 'Removeu evolução do paciente ' . $paciente);

        return redirect()->route('evolucoes.index')->with('success', 'Evolução removida.');
    }

    // 📲 API: Listar evoluções (Flutter)
    public function indexJson(Request $request)
    {
        $evolucoes = Evolucao::with('paciente')
            ->whereHas('paciente', fn($q) => $q->where('user_id', auth()->id()))
            ->orderBy('data', 'desc')
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'data' => \Carbon\Carbon::parse($e->data)->format('Y-m-d'),
                    'texto' => $e->texto,
                    'paciente' => [
                        'id' => $e->paciente->id,
                        'nome' => $e->paciente->nome,
                    ],
                ];
            });

        return response()->json($evolucoes);
    }

    // 📲 API: Criar evolução (Flutter)
    public function storeJson(Request $request)
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

        return response()->json(['message' => 'Evolução registrada com sucesso', 'evolucao' => $evolucao]);
    }

    // 📲 API: Atualizar evolução (Flutter)
    public function updateJson(Request $request, $id)
    {
        $evolucao = Evolucao::with('paciente')->findOrFail($id);

        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data' => 'required|date',
            'texto' => 'required|string',
        ]);

        $evolucao->update($request->only(['paciente_id', 'data', 'texto']));

        return response()->json(['message' => 'Evolução atualizada com sucesso', 'evolucao' => $evolucao]);
    }

    // 📲 API: Deletar evolução (Flutter)
    public function destroyJson($id)
    {
        $evolucao = Evolucao::with('paciente')->findOrFail($id);

        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        $evolucao->delete();

        return response()->json(['message' => 'Evolução removida com sucesso']);
    }
}
