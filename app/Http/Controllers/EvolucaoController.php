<?php

namespace App\Http\Controllers;

use App\Models\Evolucao;
use App\Models\Paciente;
use App\Models\Sessao;
use Illuminate\Http\Request;
use App\Helpers\AuditHelper;

class EvolucaoController extends Controller
{
    // 🌐 WEB: Listagem com filtros
    public function index(Request $request)
    {
        $query = Evolucao::with(['paciente', 'sessao'])
            ->whereHas('paciente', fn($q) => $q->where('user_id', auth()->id()));

        // 🔍 Filtro por nome do paciente
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->whereHas('paciente', fn($sub) => $sub->where('nome', 'like', "%$busca%"));
        }

        // 🔍 Filtro por tipo de evolução
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // 🔍 Filtro por período
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

        // 🔍 Filtro por vínculo de sessão
        if ($request->filled('sessao')) {
            if ($request->sessao === 'sem') {
                $query->whereNull('sessao_id');
            } elseif ($request->sessao === 'com') {
                $query->whereNotNull('sessao_id');
            }
        }

        // 📌 Ordenação e paginação
        $evolucoes = $query->orderBy('data', 'desc')->paginate(10)->withQueryString();

        AuditHelper::log('view_evolucoes', 'Visualizou a lista de evoluções clínicas');

        return view('evolucoes.index', compact('evolucoes'));
    }

    // 🌐 WEB: Formulário de criação
    public function create(Request $request)
    {
        $pacientes = Paciente::where('user_id', auth()->id())
            ->orderBy('nome', 'asc')
            ->get();

        $pacienteSelecionado = $request->paciente;
        $dataSelecionada = $request->data;
        $sessao = null;

        if ($request->filled('sessao_id')) {
        $sessao = Sessao::with('paciente')
            ->where('id', $request->sessao_id)
            ->whereHas('paciente', fn($q) => $q->where('user_id', auth()->id()))
            ->firstOrFail();

        $pacienteSelecionado = $sessao->paciente_id;
        // 👉 aqui usamos a data da sessão
        $dataSelecionada = optional($sessao->data_hora)->format('Y-m-d'); 
    }

        return view('evolucoes.create', compact('pacientes', 'pacienteSelecionado', 'dataSelecionada', 'sessao'));
}


    // 🌐 WEB: Armazenar evolução
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data' => 'required|date',
            'texto' => 'required|string',
            'sessao_id' => 'nullable|exists:sessoes,id',
        ]);

        $paciente = Paciente::where('id', $request->paciente_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $evolucao = Evolucao::create($request->only(['paciente_id', 'data', 'texto', 'sessao_id']));

        AuditHelper::log('created_evolucao', 'Adicionou evolução ao paciente ' . $paciente->nome);

        return redirect()->route('evolucoes.index')->with('success', 'Evolução registrada!');
    }

    // 🌐 WEB: Formulário de edição
    public function edit(Evolucao $evolucao)
    {
        $evolucao = Evolucao::with(['paciente', 'sessao'])->findOrFail($evolucao->id);

        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        $pacientes = Paciente::where('user_id', auth()->id())
            ->orderBy('nome', 'asc')
            ->get();
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
            'sessao_id' => 'nullable|exists:sessoes,id',
        ]);

        $paciente = Paciente::where('id', $request->paciente_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $evolucao->update($request->only(['paciente_id', 'data', 'texto', 'sessao_id']));

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

    // 🌐 AJAX: Buscar sessões por paciente
    public function getSessoes($id)
    {
        $paciente = Paciente::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $sessoes = Sessao::where('paciente_id', $paciente->id)
            ->orderBy('data_hora', 'desc')
            ->get(['id', 'data_hora']);

        return response()->json($sessoes->map(function ($s) {
            return [
                'id' => $s->id,
                'data_hora' => $s->data_hora->format('d/m/Y H:i')
            ];
        }));
    }
    
    // 🌐 WEB: Imprimir evolução
    public function imprimir(\App\Models\Evolucao $evolucao)
    {
        // segurança básica: evolução precisa pertencer ao usuário logado
        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('evolucoes.print', [
            'evolucao' => $evolucao,
            'user'     => auth()->user(),
        ]);
    }

    // 📲 API: Listar evoluções (Flutter)
    public function indexJson(Request $request)
    {
        $evolucoes = Evolucao::with(['paciente', 'sessao'])
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
                    'sessao' => $e->sessao ? [
                        'id' => $e->sessao->id,
                        'data_hora' => $e->sessao->data_hora->format('Y-m-d H:i'),
                    ] : null,
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
            'sessao_id' => 'nullable|exists:sessoes,id',
        ]);

        $paciente = Paciente::where('id', $request->paciente_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $evolucao = Evolucao::create($request->only(['paciente_id', 'data', 'texto', 'sessao_id']));

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
            'sessao_id' => 'nullable|exists:sessoes,id',
        ]);

        $evolucao->update($request->only(['paciente_id', 'data', 'texto', 'sessao_id']));

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
