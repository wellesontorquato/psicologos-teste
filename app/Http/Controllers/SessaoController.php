<?php

namespace App\Http\Controllers;

use App\Models\Sessao;
use App\Models\Paciente;
use App\Imports\SessoesImport;
use App\Exports\ModeloImportacaoSessoesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\AuditHelper;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SessoesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\GoogleCalendarService;

class SessaoController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Sessao::with('paciente')
            ->whereHas('paciente', fn ($q) => $q->where('user_id', auth()->id()));

        if ($request->filled('foi_pago')) {
            $baseQuery->where('foi_pago', $request->foi_pago === 'Sim');
        }

        if ($request->filled('status') && $request->status !== 'Todos') {
            $baseQuery->where('status_confirmacao', $request->status);
        }

        if ($request->filled('busca')) {
            $busca = preg_replace('/\D/', '', $request->busca);
            $baseQuery->whereHas('paciente', function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('telefone', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') LIKE ?", ["%$busca%"]);
            });
        }

        if ($request->filled('periodo')) {
            $hoje = \Carbon\Carbon::now('America/Sao_Paulo')->startOfDay();
            if ($request->periodo === 'hoje') {
                $baseQuery->whereBetween('data_hora', [$hoje, $hoje->copy()->endOfDay()]);
            } elseif ($request->periodo === 'semana') {
                $baseQuery->whereBetween('data_hora', [$hoje->copy()->startOfWeek(), $hoje->copy()->endOfWeek()]);
            } elseif ($request->periodo === 'proxima') {
                $baseQuery->whereBetween('data_hora', [
                    $hoje->copy()->addWeek()->startOfWeek(),
                    $hoje->copy()->addWeek()->endOfWeek()
                ]);
            }
        }

        if ($request->ordenar === 'mais_antigo') {
            $baseQuery->orderByRaw("
                CASE
                    WHEN status_confirmacao IN ('CANCELADA', 'CONFIRMADA', 'REMARCAR', 'REMARCADO') THEN 0
                    ELSE 1
                END,
                ISNULL(data_hora),
                data_hora ASC
            ");
        } else {
            $baseQuery->orderByRaw("
                CASE
                    WHEN status_confirmacao IN ('CANCELADA', 'CONFIRMADA', 'REMARCAR', 'REMARCADO') THEN 0
                    ELSE 1
                END,
                ISNULL(data_hora),
                data_hora DESC
            ");
        }

        $agora = \Carbon\Carbon::now('America/Sao_Paulo');

        $sessoesMarcadas = (clone $baseQuery)
            ->where(function ($q) use ($agora) {
                $q->whereNull('data_hora')
                  ->orWhere('data_hora', '>=', $agora);
            })
            ->paginate(10, ['*'], 'marcadas')
            ->withQueryString();

        $sessoesRealizadas = (clone $baseQuery)
            ->where('data_hora', '<', $agora)
            ->paginate(10, ['*'], 'realizadas')
            ->withQueryString();

        AuditHelper::log('view_sessoes', 'Visualizou a lista de sessões');

        return view('sessoes.index', [
            'sessoesMarcadas' => $sessoesMarcadas,
            'sessoesRealizadas' => $sessoesRealizadas,
            'filtros' => $request->only(['foi_pago', 'status', 'busca', 'periodo']),
        ]);
    }

    public function create()
    {
        $pacientes = Paciente::where('user_id', auth()->id())
            ->orderBy('nome', 'asc')
            ->get();
        return view('sessoes.create', compact('pacientes'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data_hora'   => 'required|date',
            'duracao'     => 'required|integer|min:1',
            'valor'       => 'nullable|numeric',
        ]);

        $dados['duracao'] = (int) $dados['duracao'];
        $dados['foi_pago'] = $request->has('foi_pago');
        $dados['data_hora_original'] = $dados['data_hora'];

        $inicio = Carbon::parse($dados['data_hora']);
        $fim    = $inicio->copy()->addMinutes($dados['duracao']);

        $conflito = Sessao::whereHas('paciente', fn($q) => $q->where('user_id', auth()->id()))
            ->where('data_hora', '<', $fim)
            ->whereRaw("ADDTIME(data_hora, SEC_TO_TIME(duracao * 60)) > ?", [$inicio])
            ->exists();

        if ($conflito) {
            return redirect()->back()->withInput()->with('error', 'Já existe uma sessão marcada nesse horário.');
        }

        $dados['user_id'] = auth()->id();
        $sessao = Sessao::create($dados);

        AuditHelper::log('created_sessao', 'Criou sessão com o paciente ID ' . $sessao->paciente_id);

        // Google Calendar - criar evento
        $user = $request->user();
        if ($user->google_connected) {
            try {
                $gcal = app(GoogleCalendarService::class);
                $eventId = $gcal->createEvent($user, [
                    'summary'     => "Sessão com {$sessao->paciente->nome}",
                    'description' => $sessao->observacoes ?? null,
                    'start'       => $inicio,
                    'end'         => $fim,
                    'attendees'   => $sessao->paciente->email ? [['email' => $sessao->paciente->email]] : [],
                ]);

                $sessao->update([
                    'google_event_id'    => $eventId,
                    'google_sync_status' => 'ok',
                    'google_sync_error'  => null,
                ]);
            } catch (\Throwable $e) {
                $sessao->update([
                    'google_sync_status' => 'error',
                    'google_sync_error'  => substr($e->getMessage(), 0, 1000),
                ]);
            }
        }

        return redirect()->route('sessoes.index')->with('success', 'Sessão cadastrada!');
    }

    public function storeJson(Request $request)
    {
        $dados = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'data_hora'   => 'required|date',
            'duracao'     => 'required|integer|min:1',
            'valor'       => 'nullable|numeric',
            'foi_pago'    => 'boolean',
        ]);

        $dados['duracao'] = (int) $dados['duracao'];
        $dados['data_hora_original'] = $dados['data_hora'];

        $inicio = Carbon::parse($dados['data_hora']);
        $fim    = $inicio->copy()->addMinutes($dados['duracao']);

        $conflito = Sessao::where('data_hora', '<', $fim)
            ->whereRaw("ADDTIME(data_hora, SEC_TO_TIME(duracao * 60)) > ?", [$inicio])
            ->exists();

        if ($conflito) {
            return response()->json(['message' => 'Já existe uma sessão nesse horário.'], 409);
        }

        $dados['user_id'] = auth()->id();
        $sessao = Sessao::create($dados);

        AuditHelper::log('created_sessao_json', 'Criou sessão via JSON para o paciente ID ' . $sessao->paciente_id);

        // Google Calendar - criar evento
        $user = $request->user();
        if ($user->google_connected) {
            try {
                $gcal = app(GoogleCalendarService::class);
                $eventId = $gcal->createEvent($user, [
                    'summary'     => "Sessão com {$sessao->paciente->nome}",
                    'description' => $sessao->observacoes ?? null,
                    'start'       => $inicio,
                    'end'         => $fim,
                    'attendees'   => $sessao->paciente->email ? [['email' => $sessao->paciente->email]] : [],
                ]);

                $sessao->update([
                    'google_event_id'    => $eventId,
                    'google_sync_status' => 'ok',
                    'google_sync_error'  => null,
                ]);
            } catch (\Throwable $e) {
                $sessao->update([
                    'google_sync_status' => 'error',
                    'google_sync_error'  => substr($e->getMessage(), 0, 1000),
                ]);
            }
        }

        return response()->json(['message' => 'Sessão criada com sucesso', 'id' => $sessao->id], 201);
    }

    public function edit($id)
    {
        $sessao = Sessao::with('paciente')->findOrFail($id);

        if (!$sessao->paciente || $sessao->paciente->user_id !== auth()->id()) {
            abort(403, 'ACESSO NEGADO À SESSÃO.');
        }

        AuditHelper::log('edit_sessao', 'Acessou edição da sessão ID ' . $id);

        $pacientes = Paciente::where('user_id', auth()->id())
            ->orderBy('nome', 'asc')
            ->get();
        return view('sessoes.edit', compact('sessao', 'pacientes'));
    }

    public function editJson($id)
    {
        $sessao = Sessao::with('paciente')->findOrFail($id);

        if (!$sessao->paciente || $sessao->paciente->user_id !== auth()->id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        // NÃO sobrescreve data_hora com string; cria um campo formatado
        $dataHoraLocal = Carbon::parse($sessao->data_hora)
            ->timezone(config('app.timezone'))
            ->format('Y-m-d\TH:i');

        AuditHelper::log('edit_sessao_json', 'Acessou edição JSON da sessão ID ' . $id);

        return response()->json([
            'id'          => $sessao->id,
            'paciente_id' => $sessao->paciente_id,
            'data_hora'   => $dataHoraLocal,
            'valor'       => $sessao->valor,
            'duracao'     => $sessao->duracao,
            'foi_pago'    => $sessao->foi_pago,
        ]);
    }

    public function update(Request $request, $id)
    {
        $sessao = Sessao::with('paciente')->findOrFail($id);

        if (!$sessao->paciente || $sessao->paciente->user_id !== auth()->id()) {
            abort(403, 'ACESSO NEGADO À SESSÃO.');
        }

        $dados = $request->validate([
            'paciente_id'        => 'required|exists:pacientes,id',
            'data_hora'          => 'required|date',
            'duracao'            => 'required|integer|min:1',
            'valor'              => 'nullable|numeric',
            'status_confirmacao' => 'nullable|string',
            'foi_pago'           => 'boolean',
        ]);

        $statusAntigo = $sessao->status_confirmacao;
        $dados['foi_pago'] = $request->boolean('foi_pago');

        $sessao->update($dados);

        if (is_null($sessao->data_hora_original)) {
            $sessao->update(['data_hora_original' => $dados['data_hora']]);
        }

        if ($statusAntigo !== 'CONFIRMADO' && $sessao->status_confirmacao === 'CONFIRMADO') {
            event(new \App\Events\SessaoConfirmada($sessao));
        }

        AuditHelper::log('updated_sessao', 'Atualizou sessão ID ' . $id);

        // Google Calendar - atualizar (ou criar se não existir)
        $user = $request->user();
        if ($user->google_connected) {
            try {
                $inicio = Carbon::parse($sessao->data_hora);
                $fim    = $inicio->copy()->addMinutes((int) $sessao->duracao);

                $gcal = app(GoogleCalendarService::class);

                if ($sessao->google_event_id) {
                    $gcal->updateEvent($user, $sessao->google_event_id, [
                        'summary'     => "Sessão com {$sessao->paciente->nome}",
                        'description' => $sessao->observacoes ?? null,
                        'start'       => $inicio,
                        'end'         => $fim,
                    ]);
                } else {
                    $eventId = $gcal->createEvent($user, [
                        'summary'     => "Sessão com {$sessao->paciente->nome}",
                        'description' => $sessao->observacoes ?? null,
                        'start'       => $inicio,
                        'end'         => $fim,
                        'attendees'   => $sessao->paciente->email ? [['email' => $sessao->paciente->email]] : [],
                    ]);
                    $sessao->google_event_id = $eventId;
                }

                $sessao->google_sync_status = 'ok';
                $sessao->google_sync_error  = null;
                $sessao->save();
            } catch (\Throwable $e) {
                $sessao->update([
                    'google_sync_status' => 'error',
                    'google_sync_error'  => substr($e->getMessage(), 0, 1000),
                ]);
            }
        }

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
            'data_hora'   => 'required|date',
            'duracao'     => 'required|integer|min:1',
            'valor'       => 'nullable|numeric',
            'foi_pago'    => 'boolean',
        ]);

        $dados['duracao'] = (int) $dados['duracao'];

        $horarioAlterado = $dados['data_hora'] !== $sessao->data_hora || $dados['duracao'] !== (int)$sessao->duracao;

        if ($horarioAlterado) {
            $inicio = Carbon::parse($dados['data_hora']);
            $fim    = $inicio->copy()->addMinutes($dados['duracao']);

            $conflito = Sessao::where('id', '!=', $id)
                ->where('data_hora', '<', $fim)
                ->whereRaw("ADDTIME(data_hora, SEC_TO_TIME(duracao * 60)) > ?", [$inicio])
                ->exists();

            if ($conflito) {
                return response()->json(['message' => 'Já existe uma sessão nesse horário.'], 409);
            }
        }

        $sessao->update($dados);

        if (is_null($sessao->data_hora_original)) {
            $sessao->update(['data_hora_original' => $dados['data_hora']]);
        }

        if ($sessao->wasChanged('status_confirmacao') && $sessao->status_confirmacao === 'CONFIRMADO') {
            event(new \App\Events\SessaoConfirmada($sessao));
        }

        AuditHelper::log('updated_sessao_json', 'Atualizou sessão via JSON ID ' . $id);

        // Google Calendar - atualizar (ou criar)
        $user = $request->user();
        if ($user->google_connected) {
            try {
                $inicio = Carbon::parse($sessao->data_hora);
                $fim    = $inicio->copy()->addMinutes((int) $sessao->duracao);

                $gcal = app(GoogleCalendarService::class);

                if ($sessao->google_event_id) {
                    $gcal->updateEvent($user, $sessao->google_event_id, [
                        'summary'     => "Sessão com {$sessao->paciente->nome}",
                        'description' => $sessao->observacoes ?? null,
                        'start'       => $inicio,
                        'end'         => $fim,
                    ]);
                } else {
                    $eventId = $gcal->createEvent($user, [
                        'summary'     => "Sessão com {$sessao->paciente->nome}",
                        'description' => $sessao->observacoes ?? null,
                        'start'       => $inicio,
                        'end'         => $fim,
                        'attendees'   => $sessao->paciente->email ? [['email' => $sessao->paciente->email]] : [],
                    ]);
                    $sessao->google_event_id = $eventId;
                    $sessao->save();
                }

                $sessao->google_sync_status = 'ok';
                $sessao->google_sync_error  = null;
                $sessao->save();
            } catch (\Throwable $e) {
                $sessao->update([
                    'google_sync_status' => 'error',
                    'google_sync_error'  => substr($e->getMessage(), 0, 1000),
                ]);
            }
        }

        return response()->json(['message' => 'Sessão atualizada com sucesso']);
    }

    public function destroy(Request $request, $id)
    {
        $sessao = Sessao::with('paciente')->findOrFail($id);

        if (!$sessao->paciente || $sessao->paciente->user_id !== auth()->id()) {
            abort(403, 'ACESSO NEGADO À SESSÃO.');
        }

        // Google Calendar - deletar evento
        $user = $request->user();
        if ($user->google_connected) {
            try {
                app(GoogleCalendarService::class)->deleteEvent($user, $sessao->google_event_id);
            } catch (\Throwable $e) {
                // opcional: logar erro
            }
        }

        $sessao->delete();

        AuditHelper::log('deleted_sessao', 'Excluiu sessão ID ' . $id);

        $fragmento = $request->input('aba', 'futuras');
        $queryString = $request->input('query_string');

        $redirect = redirect()->route('sessoes.index');

        if ($queryString) {
            $redirect->setTargetUrl(route('sessoes.index') . '?' . $queryString . '#' . $fragmento);
        } else {
            $redirect->withFragment($fragmento);
        }

        return $redirect->with('success', 'Sessão excluída.');
    }

    public function destroyJson($id)
    {
        $sessao = Sessao::with('paciente')->findOrFail($id);

        if (!$sessao->paciente || $sessao->paciente->user_id !== auth()->id()) {
            return response()->json(['message' => 'ACESSO NEGADO À SESSÃO.'], 403);
        }

        // Google Calendar - deletar evento
        $user = auth()->user();
        if ($user && $user->google_connected) {
            try {
                app(GoogleCalendarService::class)->deleteEvent($user, $sessao->google_event_id);
            } catch (\Throwable $e) {
                // opcional: logar erro
            }
        }

        $sessao->delete();

        \App\Helpers\AuditHelper::log('deleted_sessao', 'Excluiu sessão ID ' . $id);

        return response()->json(['message' => 'Sessão excluída com sucesso.'], 200);
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

    public function importarView()
    {
        return view('sessoes.importar');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new SessoesImport(auth()->id()), $request->file('arquivo'));

        return redirect()->route('sessoes.index')->with('sucesso', 'Sessões importadas com sucesso!');
    }

    public function baixarModeloImportacao()
    {
        return Excel::download(new ModeloImportacaoSessoesExport(auth()->id()), 'modelo_importacao_sessoes.xlsx');
    }

    public function gerarRecorrencias(Request $request)
    {
        $request->validate([
            'sessao_id' => 'required|exists:sessoes,id',
            'semanas'   => 'required|integer|min:1',
        ]);

        $sessaoOriginal = Sessao::with('paciente')->findOrFail($request->sessao_id);

        if (!$sessaoOriginal->paciente || $sessaoOriginal->paciente->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado à sessão.');
        }

        $semanas = (int) $request->semanas;
        $foiPago = $request->has('foi_pago');
        $criadas = 0;

        $user = $request->user();

        for ($i = 1; $i <= $semanas; $i++) {
            $novaDataHora = Carbon::parse($sessaoOriginal->data_hora)->addWeeks($i);
            $inicio = $novaDataHora->copy();
            $fim    = $inicio->copy()->addMinutes($sessaoOriginal->duracao);

            $conflito = Sessao::whereHas('paciente', fn($q) => $q->where('user_id', auth()->id()))
                ->where('data_hora', '<', $fim)
                ->whereRaw("ADDTIME(data_hora, SEC_TO_TIME(duracao * 60)) > ?", [$inicio])
                ->exists();

            if (!$conflito) {
                $nova = Sessao::create([
                    'user_id'            => auth()->id(),
                    'paciente_id'        => $sessaoOriginal->paciente_id,
                    'data_hora'          => $novaDataHora,
                    'data_hora_original' => $novaDataHora,
                    'duracao'            => $sessaoOriginal->duracao,
                    'valor'              => $sessaoOriginal->valor,
                    'foi_pago'           => $foiPago,
                    'observacoes'        => 'Recorrência automática da sessão ID #' . $sessaoOriginal->id,
                ]);

                // Google: criar evento da recorrência
                if ($user->google_connected) {
                    try {
                        $gcal = app(GoogleCalendarService::class);
                        $eventId = $gcal->createEvent($user, [
                            'summary'     => "Sessão com {$nova->paciente->nome}",
                            'description' => $nova->observacoes ?? null,
                            'start'       => $inicio,
                            'end'         => $fim,
                            'attendees'   => $nova->paciente->email ? [['email' => $nova->paciente->email]] : [],
                        ]);
                        $nova->update([
                            'google_event_id'    => $eventId,
                            'google_sync_status' => 'ok',
                            'google_sync_error'  => null,
                        ]);
                    } catch (\Throwable $e) {
                        $nova->update([
                            'google_sync_status' => 'error',
                            'google_sync_error'  => substr($e->getMessage(), 0, 1000),
                        ]);
                    }
                }

                $criadas++;
            }
        }

        AuditHelper::log('gerou_recorrencias', "Criou {$criadas} recorrências a partir da sessão ID {$sessaoOriginal->id}");

        return redirect()->route('sessoes.index')->with('success', "{$criadas} sessão(ões) recorrente(s) criada(s) com sucesso!");
    }

    public function gerarRecorrenciasJson(Request $request)
    {
        $dados = $request->validate([
            'sessao_id' => 'required|exists:sessoes,id',
            'semanas'   => 'required|integer|min:1',
            'foi_pago'  => 'boolean',
        ]);

        $sessaoOriginal = Sessao::with('paciente')->findOrFail($dados['sessao_id']);

        if (!$sessaoOriginal->paciente || $sessaoOriginal->paciente->user_id !== auth()->id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $semanas = (int) $dados['semanas'];
        $foiPago = $dados['foi_pago'] ?? false;
        $criadas = 0;

        $user = auth()->user();

        for ($i = 1; $i <= $semanas; $i++) {
            $novaDataHora = Carbon::parse($sessaoOriginal->data_hora)->addWeeks($i);
            $inicio = $novaDataHora->copy();
            $fim    = $inicio->copy()->addMinutes($sessaoOriginal->duracao);

            $conflito = Sessao::whereHas('paciente', fn($q) => $q->where('user_id', auth()->id()))
                ->where('data_hora', '<', $fim)
                ->whereRaw("ADDTIME(data_hora, SEC_TO_TIME(duracao * 60)) > ?", [$inicio])
                ->exists();

            if (!$conflito) {
                $nova = Sessao::create([
                    'user_id'            => auth()->id(),
                    'paciente_id'        => $sessaoOriginal->paciente_id,
                    'data_hora'          => $novaDataHora,
                    'data_hora_original' => $novaDataHora,
                    'duracao'            => $sessaoOriginal->duracao,
                    'valor'              => $sessaoOriginal->valor,
                    'foi_pago'           => $foiPago,
                    'observacoes'        => 'Recorrência automática da sessão ID #' . $sessaoOriginal->id,
                ]);

                // Google: criar evento da recorrência
                if ($user->google_connected) {
                    try {
                        $gcal = app(GoogleCalendarService::class);
                        $eventId = $gcal->createEvent($user, [
                            'summary'     => "Sessão com {$nova->paciente->nome}",
                            'description' => $nova->observacoes ?? null,
                            'start'       => $inicio,
                            'end'         => $fim,
                            'attendees'   => $nova->paciente->email ? [['email' => $nova->paciente->email]] : [],
                        ]);
                        $nova->update([
                            'google_event_id'    => $eventId,
                            'google_sync_status' => 'ok',
                            'google_sync_error'  => null,
                        ]);
                    } catch (\Throwable $e) {
                        $nova->update([
                            'google_sync_status' => 'error',
                            'google_sync_error'  => substr($e->getMessage(), 0, 1000),
                        ]);
                    }
                }

                $criadas++;
            }
        }

        AuditHelper::log('gerou_recorrencias_json', "Criou {$criadas} recorrências via API para a sessão ID {$sessaoOriginal->id}");

        return response()->json([
            'message' => "{$criadas} sessão(ões) recorrente(s) criada(s) com sucesso!"
        ], 201);
    }

    public function indexJson(Request $request)
    {
        $sessoes = Sessao::with('paciente')
            ->whereHas('paciente', fn ($q) => $q->where('user_id', auth()->id()))
            ->orderBy('data_hora', 'desc')
            ->get();

        return response()->json($sessoes);
    }
}
