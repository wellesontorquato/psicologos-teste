<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Sessao;
use App\Models\Evolucao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Helpers\AuditHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PacienteController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Paciente::where('user_id', auth()->id());

        if ($request->filled('busca')) {
            $busca = preg_replace('/\D/', '', $request->busca);
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('telefone', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') LIKE ?", ["%$busca%"]);
            });
        }

        $pacientes = $query->orderBy('nome')->paginate(10)->withQueryString();

        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        if ($request->filled('sem_numero')) {
            $request->merge(['numero' => 'S/N']);
        }

        $this->sanitizarDados($request);

        $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'sexo' => 'required|string|in:M,F,Outro',
            'telefone' => ['required', 'string', 'max:20', $this->validarTelefoneUnico()],
            'email' => ['required', 'email', 'max:255', $this->validarEmailUnico()],
            'cpf' => ['required', 'string', 'max:20', $this->validarCpfUnico()],
            'cep' => 'nullable|string|max:10',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'exige_nota_fiscal' => 'nullable|in:on,1,true,0,false',
            'observacoes' => 'nullable|string',
            'medicacao_inicial' => 'nullable|string|max:255',
            'nome_contato_emergencia' => 'nullable|string|max:255',
            'telefone_contato_emergencia' => 'nullable|string|max:20',
            'parentesco_contato_emergencia' => 'nullable|string|max:50',
        ]);

        $paciente = Paciente::create($request->except('medicacao_inicial') + [
            'user_id' => auth()->id(),
            'exige_nota_fiscal' => (bool) $request->input('exige_nota_fiscal', false),
        ]);

        if ($request->filled('medicacao_inicial')) {
            Evolucao::create([
                'paciente_id' => $paciente->id,
                'data' => now(),
                'texto' => 'Medicação Inicial: ' . $request->medicacao_inicial,
                'tipo' => 'medicacao',
            ]);
        }

        AuditHelper::log('created_paciente', 'Criou o paciente ' . $paciente->nome);

        if ($request->ajax()) {
            return response()->json(['message' => 'Paciente cadastrado com sucesso!']);
        }

        return redirect()->route('pacientes.index')->with('success', 'Paciente cadastrado com sucesso!');
    }

    public function edit(Paciente $paciente)
    {
        $this->authorize('update', $paciente);
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $this->authorize('update', $paciente);

        if ($request->filled('sem_numero')) {
            $request->merge(['numero' => 'S/N']);
        }

        $this->sanitizarDados($request);

        $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'nullable|date',
            'sexo' => 'nullable|string|max:10',
            'telefone' => ['nullable', 'string', 'max:20', $this->validarTelefoneUnico($paciente->id)],
            'email' => ['nullable', 'email', 'max:255', $this->validarEmailUnico($paciente->id)],
            'cpf' => ['nullable', 'string', 'max:20', $this->validarCpfUnico($paciente->id)],
            'cep' => 'nullable|string|max:10',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'exige_nota_fiscal' => 'nullable|in:on,1,true,0,false',
            'observacoes' => 'nullable|string',
            'nova_medicacao' => 'nullable|string|max:255',
            'nome_contato_emergencia' => 'nullable|string|max:255',
            'telefone_contato_emergencia' => 'nullable|string|max:20',
            'parentesco_contato_emergencia' => 'nullable|string|max:50',
        ]);

        $paciente->update($request->except('nova_medicacao') + [
            'exige_nota_fiscal' => (bool) $request->input('exige_nota_fiscal', false),
        ]);

        if ($request->filled('nova_medicacao')) {
            Evolucao::create([
                'paciente_id' => $paciente->id,
                'data' => now(),
                'texto' => 'Medicação registrada: ' . $request->nova_medicacao,
                'tipo' => 'medicacao',
            ]);
        }

        AuditHelper::log('updated_paciente', 'Atualizou o paciente ' . $paciente->nome);

        if ($request->ajax()) {
            return response()->json(['message' => 'Paciente atualizado com sucesso!']);
        }

        return redirect()->route('pacientes.index')->with('success', 'Paciente atualizado!');
    }

    public function destroy(Paciente $paciente)
    {
        $this->authorize('delete', $paciente);
        $nome = $paciente->nome;
        $paciente->delete();

        AuditHelper::log('deleted_paciente', 'Removeu o paciente ' . $nome);

        return redirect()->route('pacientes.index')->with('success', 'Paciente removido!');
    }

    public function historico(Paciente $paciente)
    {
        $eventos = $this->gerarEventosDoPaciente($paciente);

        // Agrupa por data (mantendo ordem)
        $eventosAgrupados = collect($eventos)
            ->groupBy('data')
            ->sortKeys();

        // PAGINAÇÃO MANUAL DOS BLOCOS (cada grupo = 1 dia)
        $paginaAtual = LengthAwarePaginator::resolveCurrentPage();
        $itensPorPagina = 5;
        $itemsPaginados = $eventosAgrupados->forPage($paginaAtual, $itensPorPagina);

        $paginador = new LengthAwarePaginator(
            $itemsPaginados,
            $eventosAgrupados->count(),
            $itensPorPagina,
            $paginaAtual,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('pacientes.historico', [
            'paciente' => $paciente,
            'eventos' => $paginador,
            'eventosAgrupados' => $paginador
        ]);
    }

    public function exportarHistoricoPdf(Paciente $paciente)
    {
        $this->authorize('exportarHistorico', $paciente);
        $eventos = $this->gerarEventosDoPaciente($paciente);

        AuditHelper::log('exported_historico', 'Exportou o histórico do paciente ' . $paciente->nome);

        $pdf = Pdf::loadView('pacientes.historico_pdf', compact('paciente', 'eventos'))->setPaper('a4', 'portrait');

        return $pdf->download("historico_{$paciente->nome}.pdf");
    }

    private function gerarEventosDoPaciente(Paciente $paciente): array
    {
        $sessoes = $paciente->sessoes()
            ->where('status_confirmacao', 'CONFIRMADA')
            ->orderBy('data_hora')
            ->get();

        $evolucoes = $paciente->evolucoes()
            ->orderBy('data')
            ->get();

        $eventos = [];

        foreach ($sessoes as $sessao) {
            $data = Carbon::parse($sessao->data_hora);
            $eventos[] = [
                'tipo' => 'Sessão',
                'data' => $data->format('Y-m-d'),
                'hora' => $data->format('H:i'),
                'status_confirmacao' => $sessao->status_confirmacao,
                'descricao' => 'Valor: R$ ' . number_format($sessao->valor, 2, ',', '.') .
                    ($sessao->foi_pago ? ' <span class="text-success">(Pago)</span>' : ' <span class="text-danger">(Pendente)</span>')
            ];
        }

        foreach ($evolucoes as $evolucao) {
            $data = Carbon::parse($evolucao->data);
            $eventos[] = [
                'tipo' => $evolucao->tipo === 'medicacao' ? 'Medicação' : 'Evolução',
                'data' => $data->format('Y-m-d'),
                'hora' => '',
                'status' => 'confirmado',
                'descricao' => nl2br(e(trim($evolucao->texto) ?: 'Sem anotação registrada.'))
            ];
        }

        usort($eventos, fn($a, $b) => strtotime($a['data'] . ' ' . ($a['hora'] ?? '00:00')) <=> strtotime($b['data'] . ' ' . ($b['hora'] ?? '00:00')));

        return $eventos;
    }

    public function aniversariantesHoje()
    {
        $hoje = Carbon::today();

        $pacientes = Paciente::where('user_id', auth()->id())
            ->whereMonth('data_nascimento', $hoje->month)
            ->whereDay('data_nascimento', $hoje->day)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'nome' => $p->nome,
                'idade' => Carbon::parse($p->data_nascimento)->age,
            ]);

        return response()->json($pacientes);
    }

    public function indexJson()
    {
        $pacientes = Paciente::where('user_id', auth()->id())
            ->select('id', 'nome', 'telefone')
            ->orderBy('nome')
            ->get();

        return response()->json($pacientes);
    }

    private function sanitizarDados(Request &$request)
    {
        foreach (['cpf', 'telefone', 'telefone_contato_emergencia'] as $campo) {
            if ($request->filled($campo)) {
                $request->merge([$campo => preg_replace('/\D/', '', $request->$campo)]);
            }
        }
    }

    private function validarTelefoneUnico($ignoreId = null)
    {
        return function ($attribute, $value, $fail) use ($ignoreId) {
            $query = Paciente::where('user_id', auth()->id())
                ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telefone, '(', ''), ')', ''), '-', ''), ' ', ''), '+', '') = ?", [preg_replace('/\D/', '', $value)]);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            if ($query->exists()) {
                $fail('Este telefone já está cadastrado para outro paciente.');
            }
        };
    }

    private function validarEmailUnico($ignoreId = null)
    {
        return function ($attribute, $value, $fail) use ($ignoreId) {
            $query = Paciente::where('user_id', auth()->id())->where('email', $value);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            if ($query->exists()) {
                $fail('Este e-mail já está cadastrado para outro paciente.');
            }
        };
    }

    private function validarCpfUnico($ignoreId = null)
    {
        return function ($attribute, $value, $fail) use ($ignoreId) {
            $cpfLimpo = preg_replace('/\D/', '', $value);
            $query = Paciente::where('user_id', auth()->id())
                ->whereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ?", [$cpfLimpo]);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            if ($query->exists()) {
                $fail('Este CPF já está cadastrado para outro paciente.');
            }
        };
    }

    //API FLUTTER

        public function showJson($id)
    {
        $paciente = Paciente::where('user_id', auth()->id())->findOrFail($id);
        return response()->json($paciente);
    }

    public function storeJson(Request $request)
    {
        $this->sanitizarDados($request);

        $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'sexo' => 'required|string|in:M,F,Outro',
            'telefone' => ['required', 'string', 'max:20', $this->validarTelefoneUnico()],
            'email' => ['required', 'email', 'max:255', $this->validarEmailUnico()],
            'cpf' => ['required', 'string', 'max:20', $this->validarCpfUnico()],
        ]);

        $paciente = Paciente::create([
            ...$request->only([
                'nome', 'data_nascimento', 'sexo', 'telefone', 'email', 'cpf',
                'cep', 'rua', 'numero', 'complemento', 'bairro', 'cidade', 'uf',
                'observacoes', 'nome_contato_emergencia', 'telefone_contato_emergencia', 'parentesco_contato_emergencia'
            ]),
            'user_id' => auth()->id(),
            'exige_nota_fiscal' => (bool) $request->input('exige_nota_fiscal', false),
        ]);

        if ($request->filled('medicacao_inicial')) {
            Evolucao::create([
                'paciente_id' => $paciente->id,
                'data' => now(),
                'texto' => 'Medicação Inicial: ' . $request->medicacao_inicial,
                'tipo' => 'medicacao',
            ]);
        }

        AuditHelper::log('created_paciente', 'Criou o paciente ' . $paciente->nome);

        return response()->json(['message' => 'Paciente cadastrado com sucesso!', 'paciente' => $paciente], 201);
    }

    public function updateJson(Request $request, $id)
    {
        $paciente = Paciente::findOrFail($id);
        $this->authorize('update', $paciente);

        $this->sanitizarDados($request);

        $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'nullable|date',
            'sexo' => 'nullable|string|in:M,F,Outro',
            'telefone' => ['nullable', 'string', 'max:20', $this->validarTelefoneUnico($id)],
            'email' => ['nullable', 'email', 'max:255', $this->validarEmailUnico($id)],
            'cpf' => ['nullable', 'string', 'max:20', $this->validarCpfUnico($id)],
        ]);

        $paciente->update([
            ...$request->only([
                'nome', 'data_nascimento', 'sexo', 'telefone', 'email', 'cpf',
                'cep', 'rua', 'numero', 'complemento', 'bairro', 'cidade', 'uf',
                'observacoes', 'nome_contato_emergencia', 'telefone_contato_emergencia', 'parentesco_contato_emergencia'
            ]),
            'exige_nota_fiscal' => (bool) $request->input('exige_nota_fiscal', false),
        ]);

        if ($request->filled('nova_medicacao')) {
            Evolucao::create([
                'paciente_id' => $paciente->id,
                'data' => now(),
                'texto' => 'Medicação registrada: ' . $request->nova_medicacao,
                'tipo' => 'medicacao',
            ]);
        }

        AuditHelper::log('updated_paciente', 'Atualizou o paciente ' . $paciente->nome);

        return response()->json(['message' => 'Paciente atualizado com sucesso!', 'paciente' => $paciente]);
    }

    public function destroyJson($id)
    {
        $paciente = Paciente::findOrFail($id);
        $this->authorize('delete', $paciente);
        $nome = $paciente->nome;
        $paciente->delete();

        AuditHelper::log('deleted_paciente', 'Removeu o paciente ' . $nome);

        return response()->json(['message' => 'Paciente removido com sucesso!']);
    }


}
