<?php

namespace App\Http\Controllers;

use App\Models\Evolucao;
use App\Models\EvolucaoIndicador;
use App\Models\Paciente;
use App\Models\Sessao;
use Illuminate\Http\Request;
use App\Helpers\AuditHelper;

class EvolucaoController extends Controller
{
    // 🌐 WEB: Listagem com filtros
    public function index(Request $request)
    {
        $query = Evolucao::with(['paciente', 'sessao', 'indicador'])
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

            if (!empty($sessao->data_hora)) {
                $dt = $sessao->data_hora instanceof \Carbon\Carbon
                    ? $sessao->data_hora
                    : \Carbon\Carbon::parse($sessao->data_hora);
                $dataSelecionada = $dt->format('Y-m-d');
            } else {
                $dataSelecionada = null;
            }
        }

        return view('evolucoes.create', compact('pacientes', 'pacienteSelecionado', 'dataSelecionada', 'sessao'));
    }

    // 🌐 WEB: Armazenar evolução
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id'            => 'required|exists:pacientes,id',
            'data'                   => 'required|date',
            'texto'                  => 'required|string',
            'sessao_id'              => 'nullable|exists:sessoes,id',
            'estado_emocional'       => 'nullable|string|max:50',
            'intensidade'            => 'nullable|integer|between:1,5',
            'alerta'                 => 'nullable|integer|in:0,1,2',
            'indicador_observacoes'  => 'nullable|string',
        ]);

        $paciente = Paciente::where('id', $request->paciente_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $evolucao = Evolucao::create($request->only([
            'paciente_id',
            'data',
            'texto',
            'sessao_id'
        ]));

        if (
            $request->filled('estado_emocional') ||
            $request->filled('intensidade') ||
            $request->filled('alerta') ||
            $request->filled('indicador_observacoes')
        ) {
            $evolucao->indicador()->create([
                'paciente_id'      => $request->paciente_id,
                'sessao_id'        => $request->sessao_id,
                'estado_emocional' => $request->estado_emocional,
                'intensidade'      => $request->intensidade,
                'alerta'           => $request->alerta,
                'observacoes'      => $request->indicador_observacoes,
            ]);
        }

        AuditHelper::log('created_evolucao', 'Adicionou evolução ao paciente ' . $paciente->nome);

        return redirect()->route('evolucoes.index')->with('success', 'Evolução registrada!');
    }

    // 🌐 WEB: Formulário de edição
    public function edit(Evolucao $evolucao)
    {
        $evolucao->load(['paciente', 'sessao', 'indicador']);

        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        $pacientes = Paciente::where('user_id', auth()->id())
            ->orderBy('nome', 'asc')
            ->get();

        $sessoesPaciente = Sessao::where('paciente_id', $evolucao->paciente_id)
            ->whereHas('paciente', fn($q) => $q->where('user_id', auth()->id()))
            ->orderByRaw("CASE WHEN data_hora IS NULL THEN 1 ELSE 0 END, data_hora DESC")
            ->get(['id', 'data_hora', 'duracao']);

        return view('evolucoes.edit', compact('evolucao', 'pacientes', 'sessoesPaciente'));
    }

    // 🌐 WEB: Atualizar evolução
    public function update(Request $request, Evolucao $evolucao)
    {
        $evolucao->load(['paciente', 'indicador']);

        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'paciente_id'            => 'required|exists:pacientes,id',
            'data'                   => 'required|date',
            'texto'                  => 'required|string',
            'sessao_id'              => 'nullable|exists:sessoes,id',
            'estado_emocional'       => 'nullable|string|max:50',
            'intensidade'            => 'nullable|integer|between:1,5',
            'alerta'                 => 'nullable|integer|in:0,1,2',
            'indicador_observacoes'  => 'nullable|string',
        ]);

        $paciente = Paciente::where('id', $request->paciente_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $evolucao->update($request->only([
            'paciente_id',
            'data',
            'texto',
            'sessao_id'
        ]));

        if (
            $request->filled('estado_emocional') ||
            $request->filled('intensidade') ||
            $request->filled('alerta') ||
            $request->filled('indicador_observacoes')
        ) {
            $evolucao->indicador()->updateOrCreate(
                ['evolucao_id' => $evolucao->id],
                [
                    'paciente_id'      => $request->paciente_id,
                    'sessao_id'        => $request->sessao_id,
                    'estado_emocional' => $request->estado_emocional,
                    'intensidade'      => $request->intensidade,
                    'alerta'           => $request->alerta,
                    'observacoes'      => $request->indicador_observacoes,
                ]
            );
        } else {
            $evolucao->indicador()?->delete();
        }

        AuditHelper::log('updated_evolucao', 'Editou evolução do paciente ' . $paciente->nome);

        return redirect()->route('evolucoes.index')->with('success', 'Evolução atualizada.');
    }

    // 🌐 WEB: Deletar evolução
    public function destroy(Evolucao $evolucao)
    {
        $evolucao->load('paciente');

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
            ->orderByRaw("CASE WHEN data_hora IS NULL THEN 1 ELSE 0 END, data_hora DESC")
            ->get(['id', 'data_hora', 'duracao']);

        $payload = $sessoes->map(function ($s) {
            $dt = null;
            if (!empty($s->data_hora)) {
                $dt = $s->data_hora instanceof \Carbon\Carbon
                    ? $s->data_hora
                    : \Carbon\Carbon::parse($s->data_hora);
            }

            return [
                'id'        => (int)$s->id,
                'label'     => $dt
                    ? $dt->format('d/m/Y H:i') . ' (' . (int)($s->duracao ?? 0) . 'min)'
                    : 'Sem data / remarcar',
                'data_hora' => $dt ? $dt->format('Y-m-d H:i') : null,
                'duracao'   => (int)($s->duracao ?? 0),
            ];
        });

        return response()->json($payload);
    }

    // 🌐 WEB: Imprimir evolução
    public function imprimir(Evolucao $evolucao)
    {
        $evolucao->load(['paciente', 'sessao', 'indicador']);

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
        $evolucoes = Evolucao::with(['paciente', 'sessao', 'indicador'])
            ->whereHas('paciente', fn($q) => $q->where('user_id', auth()->id()))
            ->orderBy('data', 'desc')
            ->get()
            ->map(function ($e) {
                $sessao = null;

                if ($e->sessao) {
                    $dt = null;
                    if (!empty($e->sessao->data_hora)) {
                        $dt = $e->sessao->data_hora instanceof \Carbon\Carbon
                            ? $e->sessao->data_hora
                            : \Carbon\Carbon::parse($e->sessao->data_hora);
                    }
                    $sessao = [
                        'id'        => $e->sessao->id,
                        'data_hora' => $dt ? $dt->format('Y-m-d H:i') : null,
                    ];
                }

                return [
                    'id'        => $e->id,
                    'data'      => \Carbon\Carbon::parse($e->data)->format('Y-m-d'),
                    'texto'     => $e->texto,
                    'paciente'  => [
                        'id'   => $e->paciente->id,
                        'nome' => $e->paciente->nome,
                    ],
                    'sessao'    => $sessao,
                    'indicador' => $e->indicador ? [
                        'estado_emocional' => $e->indicador->estado_emocional,
                        'intensidade'      => $e->indicador->intensidade,
                        'alerta'           => $e->indicador->alerta,
                        'observacoes'      => $e->indicador->observacoes,
                    ] : null,
                ];
            });

        return response()->json($evolucoes);
    }

    // 📲 API: Criar evolução (Flutter)
    public function storeJson(Request $request)
    {
        $request->validate([
            'paciente_id'            => 'required|exists:pacientes,id',
            'data'                   => 'required|date',
            'texto'                  => 'required|string',
            'sessao_id'              => 'nullable|exists:sessoes,id',
            'estado_emocional'       => 'nullable|string|max:50',
            'intensidade'            => 'nullable|integer|between:1,5',
            'alerta'                 => 'nullable|integer|in:0,1,2',
            'indicador_observacoes'  => 'nullable|string',
        ]);

        $paciente = Paciente::where('id', $request->paciente_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $evolucao = Evolucao::create($request->only([
            'paciente_id',
            'data',
            'texto',
            'sessao_id'
        ]));

        if (
            $request->filled('estado_emocional') ||
            $request->filled('intensidade') ||
            $request->filled('alerta') ||
            $request->filled('indicador_observacoes')
        ) {
            $evolucao->indicador()->create([
                'paciente_id'      => $request->paciente_id,
                'sessao_id'        => $request->sessao_id,
                'estado_emocional' => $request->estado_emocional,
                'intensidade'      => $request->intensidade,
                'alerta'           => $request->alerta,
                'observacoes'      => $request->indicador_observacoes,
            ]);
        }

        return response()->json([
            'message'  => 'Evolução registrada com sucesso',
            'evolucao' => $evolucao->load(['paciente', 'sessao', 'indicador']),
        ]);
    }

    // 📲 API: Atualizar evolução (Flutter)
    public function updateJson(Request $request, $id)
    {
        $evolucao = Evolucao::with(['paciente', 'indicador'])->findOrFail($id);

        if (!$evolucao->paciente || $evolucao->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'paciente_id'            => 'required|exists:pacientes,id',
            'data'                   => 'required|date',
            'texto'                  => 'required|string',
            'sessao_id'              => 'nullable|exists:sessoes,id',
            'estado_emocional'       => 'nullable|string|max:50',
            'intensidade'            => 'nullable|integer|between:1,5',
            'alerta'                 => 'nullable|integer|in:0,1,2',
            'indicador_observacoes'  => 'nullable|string',
        ]);

        $paciente = Paciente::where('id', $request->paciente_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $evolucao->update($request->only([
            'paciente_id',
            'data',
            'texto',
            'sessao_id'
        ]));

        if (
            $request->filled('estado_emocional') ||
            $request->filled('intensidade') ||
            $request->filled('alerta') ||
            $request->filled('indicador_observacoes')
        ) {
            $evolucao->indicador()->updateOrCreate(
                ['evolucao_id' => $evolucao->id],
                [
                    'paciente_id'      => $request->paciente_id,
                    'sessao_id'        => $request->sessao_id,
                    'estado_emocional' => $request->estado_emocional,
                    'intensidade'      => $request->intensidade,
                    'alerta'           => $request->alerta,
                    'observacoes'      => $request->indicador_observacoes,
                ]
            );
        } else {
            $evolucao->indicador()?->delete();
        }

        return response()->json([
            'message'  => 'Evolução atualizada com sucesso',
            'evolucao' => $evolucao->load(['paciente', 'sessao', 'indicador']),
        ]);
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