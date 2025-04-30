<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\AuditHelper;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SessoesExport;
use Barryvdh\DomPDF\Facade\Pdf;

class SessaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Sessao::with('paciente')
            ->whereHas('paciente', fn ($q) => $q->where('user_id', auth()->id()));

        if ($request->filled('foi_pago')) {
            $query->where('foi_pago', $request->foi_pago === 'Sim');
        }

        if ($request->filled('status') && $request->status !== 'Todos') {
            $query->where('status_confirmacao', $request->status);
        }

        if ($request->filled('busca')) {
            $busca = preg_replace('/\D/', '', $request->busca);
            $query->whereHas('paciente', function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('telefone', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') LIKE ?", ["%$busca%"]);
            });
        }

        if ($request->filled('periodo')) {
            $hoje = \Carbon\Carbon::now('America/Sao_Paulo')->startOfDay();

            if ($request->periodo === 'hoje') {
                $query->whereBetween('data_hora', [$hoje, $hoje->copy()->endOfDay()]);
            } elseif ($request->periodo === 'semana') {
                $query->whereBetween('data_hora', [$hoje->copy()->startOfWeek(), $hoje->copy()->endOfWeek()]);
            } elseif ($request->periodo === 'proxima') {
                $query->whereBetween('data_hora', [
                    $hoje->copy()->addWeek()->startOfWeek(),
                    $hoje->copy()->addWeek()->endOfWeek()
                ]);
            }
        }

        $sessoes = $query->orderBy('data_hora', 'desc')->paginate(10);

        AuditHelper::log('view_sessoes', 'Visualizou a lista de sessões');

        return view('sessoes.index', [
            'sessoes' => $sessoes,
            'filtros' => $request->only(['foi_pago', 'status', 'busca', 'periodo']),
        ]);
    }

    public function create()
    {
        $pacientes = Paciente::where('user_id', auth()->id())->get();
        return view('sessoes.create', compact('pacientes'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data_hora' => 'required|date',
            'duracao' => 'required|integer|min:1',
            'valor' => 'nullable|numeric',
        ]);

        $dados['duracao'] = (int) $dados['duracao'];
        $dados['foi_pago'] = $request->has('foi_pago');

        $inicio = Carbon::parse($dados['data_hora']);
        $fim = $inicio->copy()->addMinutes($dados['duracao']);

        $conflito = Sessao::whereHas('paciente', fn($q) => $q->where('user_id', auth()->id()))
            ->where('data_hora', '<', $fim)
            ->whereRaw("ADDTIME(data_hora, SEC_TO_TIME(duracao * 60)) > ?", [$inicio])
            ->exists();

        if ($conflito) {
            return redirect()->back()->withInput()->with('error', 'Já existe uma sessão marcada nesse horário.');
        }

        $sessao = Sessao::create($dados);

        AuditHelper::log('created_sessao', 'Criou sessão com o paciente ID ' . $sessao->paciente_id);

        return redirect()->route('sessoes.index')->with('success', 'Sessão cadastrada!');
    }

    public function storeJson(Request $request)
    {
        $dados = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data_hora' => 'required|date',
            'duracao' => 'required|integer|min:1',
            'valor' => 'nullable|numeric',
            'foi_pago' => 'boolean',
        ]);

        $dados['duracao'] = (int) $dados['duracao'];

        $inicio = Carbon::parse($dados['data_hora']);
        $fim = $inicio->copy()->addMinutes($dados['duracao']);

        $conflito = Sessao::where('data_hora', '<', $fim)
            ->whereRaw("ADDTIME(data_hora, SEC_TO_TIME(duracao * 60)) > ?", [$inicio])
            ->exists();

        if ($conflito) {
            return response()->json(['message' => 'Já existe uma sessão nesse horário.'], 409);
        }

        $sessao = Sessao::create($dados);

        AuditHelper::log('created_sessao_json', 'Criou sessão via JSON para o paciente ID ' . $sessao->paciente_id);

        return response()->json(['message' => 'Sessão criada com sucesso', 'id' => $sessao->id], 201);
    }

    public function edit($id)
    {
        $sessao = Sessao::with('paciente')->findOrFail($id);

        if (!$sessao->paciente || $sessao->paciente->user_id !== auth()->id()) {
            abort(403, 'ACESSO NEGADO À SESSÃO.');
        }

        AuditHelper::log('edit_sessao', 'Acessou edição da sessão ID ' . $id);

        $pacientes = Paciente::where('user_id', auth()->id())->get();
        return view('sessoes.edit', compact('sessao', 'pacientes'));
    }

    public function editJson($id)
    {
        $sessao = Sessao::with('paciente')->findOrFail($id);

        if (!$sessao->paciente || $sessao->paciente->user_id !== auth()->id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $sessao->data_hora = Carbon::parse($sessao->data_hora)->format('Y-m-d\TH:i');

        AuditHelper::log('edit_sessao_json', 'Acessou edição JSON da sessão ID ' . $id);

        return response()->json($sessao);
    }

    public function update(Request $request, $id)
    {
        $sessao = Sessao::with('paciente')->findOrFail($id);

        if (!$sessao->paciente || $sessao->paciente->user_id !== auth()->id()) {
            abort(403, 'ACESSO NEGADO À SESSÃO.');
        }

        $dados = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data_hora' => 'required|date',
            'duracao' => 'required|integer|min:1',
            'valor' => 'nullable|numeric',
            'status_confirmacao' => 'nullable|string',
            'foi_pago' => 'boolean',
        ]);

        $statusAntigo = $sessao->status_confirmacao;

        $dados['foi_pago'] = $request->boolean('foi_pago');

        $sessao->update($dados);

        if ($statusAntigo !== 'CONFIRMADO' && $sessao->status_confirmacao === 'CONFIRMADO') {
            event(new \App\Events\SessaoConfirmada($sessao));
        }

        AuditHelper::log('updated_sessao', 'Atualizou sessão ID ' . $id);

        return redirect()->route('sessoes.index')->with('success', 'Sessão atualizada!');
    }


    public function updateJson(Request $request, $id)
    {
        $sessao = Sessao::with('paciente')->findOrFail($id);

        if (!$sessao->paciente || $sessao->paciente->user_id !== auth()->id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $dados = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data_hora' => 'required|date',
            'duracao' => 'required|integer|min:1',
            'valor' => 'nullable|numeric',
            'foi_pago' => 'boolean',
        ]);

        $dados['duracao'] = (int) $dados['duracao'];

        $horarioAlterado = $dados['data_hora'] !== $sessao->data_hora || $dados['duracao'] !== (int)$sessao->duracao;

        if ($horarioAlterado) {
            $inicio = Carbon::parse($dados['data_hora']);
            $fim = $inicio->copy()->addMinutes($dados['duracao']);

            $conflito = Sessao::where('id', '!=', $id)
                ->where('data_hora', '<', $fim)
                ->whereRaw("ADDTIME(data_hora, SEC_TO_TIME(duracao * 60)) > ?", [$inicio])
                ->exists();

            if ($conflito) {
                return response()->json(['message' => 'Já existe uma sessão nesse horário.'], 409);
            }
        }

        $sessao->update($dados);

        if ($sessao->wasChanged('status_confirmacao') && $sessao->status_confirmacao === 'CONFIRMADO') {
            event(new \App\Events\SessaoConfirmada($sessao));
        }

        AuditHelper::log('updated_sessao_json', 'Atualizou sessão via JSON ID ' . $id);

        return response()->json(['message' => 'Sessão atualizada com sucesso']);
    }

    public function destroy($id)
    {
        $sessao = Sessao::with('paciente')->findOrFail($id);

        if (!$sessao->paciente || $sessao->paciente->user_id !== auth()->id()) {
            abort(403, 'ACESSO NEGADO À SESSÃO.');
        }

        $sessao->delete();

        AuditHelper::log('deleted_sessao', 'Excluiu sessão ID ' . $id);

        return redirect()->route('sessoes.index')->with('success', 'Sessão excluída.');
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'pdf');

        $query = Sessao::with('paciente')
            ->whereHas('paciente', fn ($q) => $q->where('user_id', auth()->id()));

        if ($request->filled('foi_pago')) {
            $query->where('foi_pago', $request->foi_pago === 'Sim');
        }

        if ($request->filled('status') && $request->status !== 'Todos') {
            if ($request->status === 'REMARCADO') {
                $query->where('status_confirmacao', 'REMARCAR')->whereNotNull('data_hora');
            } elseif ($request->status === 'REMARCAR') {
                $query->where('status_confirmacao', 'REMARCAR')->whereNull('data_hora');
            } else {
                $query->where('status_confirmacao', $request->status);
            }
        }

        if ($request->filled('periodo')) {
            $hoje = \Carbon\Carbon::now('America/Sao_Paulo')->startOfDay();
            if ($request->periodo === 'hoje') {
                $query->whereDate('data_hora', $hoje);
            } elseif ($request->periodo === 'semana') {
                $query->whereBetween('data_hora', [$hoje->copy()->startOfWeek(), $hoje->copy()->endOfWeek()]);
            } elseif ($request->periodo === 'proxima') {
                $query->whereBetween('data_hora', [
                    $hoje->copy()->addWeek()->startOfWeek(),
                    $hoje->copy()->addWeek()->endOfWeek()
                ]);
            }
        }                

        if ($request->filled('busca')) {
            $busca = preg_replace('/\D/', '', $request->busca);
            $query->whereHas('paciente', function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('telefone', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') LIKE ?", ["%$busca%"]);
            });
        }             

        $sessoes = $query->get();

        if ($format === 'excel') {
            return Excel::download(new SessoesExport($sessoes), 'sessoes.xlsx');
        }

        $pdf = Pdf::loadView('sessoes.export-pdf', compact('sessoes'));
        return $pdf->download('sessoes.pdf');
    }
}
