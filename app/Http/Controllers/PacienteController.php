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

        if ($request->filled('cpf')) {
            $request->merge([
                'cpf' => preg_replace('/\D/', '', $request->cpf),
            ]);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'nullable|date',
            'sexo' => 'nullable|string|max:10',
            'telefone' => [
                'nullable', 'string', 'max:20',
                function ($attribute, $value, $fail) {
                    if (Paciente::where('user_id', auth()->id())->where('telefone', $value)->exists()) {
                        $fail('Este telefone já está cadastrado para outro paciente.');
                    }
                },
            ],
            'email' => [
                'nullable', 'email', 'max:255',
                function ($attribute, $value, $fail) {
                    if (Paciente::where('user_id', auth()->id())->where('email', $value)->exists()) {
                        $fail('Este e-mail já está cadastrado para outro paciente.');
                    }
                },
            ],
            'cpf' => [
                'nullable', 'string', 'max:20',
                function ($attribute, $value, $fail) {
                    if (Paciente::where('user_id', auth()->id())
                        ->whereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ?", [$value])
                        ->exists()) {
                        $fail('Este CPF já está cadastrado para outro paciente.');
                    }
                },
            ],
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
        ]);

        $paciente = Paciente::create([
            'user_id' => auth()->id(),
            'nome' => $request->nome,
            'data_nascimento' => $request->data_nascimento,
            'sexo' => $request->sexo,
            'telefone' => $request->telefone,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'cep' => $request->cep,
            'rua' => $request->rua,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'uf' => $request->uf,
            'exige_nota_fiscal' => (bool) $request->input('exige_nota_fiscal', false),
            'observacoes' => $request->observacoes,
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
            return response()->json([
                'message' => 'Paciente cadastrado com sucesso!'
            ]);
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
            if ($request->filled('sem_numero')) {
                $request->merge(['numero' => 'S/N']);
            }
        
            if ($request->filled('cpf')) {
                $request->merge([
                    'cpf' => preg_replace('/\D/', '', $request->cpf),
                ]);
            }
        
            $this->authorize('update', $paciente);
        
            $request->validate([
                'nome' => 'required|string|max:255',
                'data_nascimento' => 'nullable|date',
                'sexo' => 'nullable|string|max:10',
                'telefone' => [
                    'nullable', 'string', 'max:20',
                    function ($attribute, $value, $fail) use ($paciente) {
                        if (Paciente::where('user_id', auth()->id())
                            ->where('telefone', $value)
                            ->where('id', '!=', $paciente->id)
                            ->exists()) {
                            $fail('Este telefone já está cadastrado para outro paciente.');
                        }
                    },
                ],
                'email' => [
                    'nullable', 'email', 'max:255',
                    function ($attribute, $value, $fail) use ($paciente) {
                        if (Paciente::where('user_id', auth()->id())
                            ->where('email', $value)
                            ->where('id', '!=', $paciente->id)
                            ->exists()) {
                            $fail('Este e-mail já está cadastrado para outro paciente.');
                        }
                    },
                ],
                'cpf' => [
                    'nullable', 'string', 'max:20',
                    function ($attribute, $value, $fail) use ($paciente) {
                        if (Paciente::where('user_id', auth()->id())
                            ->whereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ?", [$value])
                            ->where('id', '!=', $paciente->id)
                            ->exists()) {
                            $fail('Este CPF já está cadastrado para outro paciente.');
                        }
                    },
                ],
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
            ]);

        // Normaliza CPF antes de atualizar
            if ($request->filled('cpf')) {
                $request->merge([
                    'cpf' => preg_replace('/\D/', '', $request->cpf),
                ]);
            }

        $paciente->update([
            'nome' => $request->nome,
            'data_nascimento' => $request->data_nascimento,
            'sexo' => $request->sexo,
            'telefone' => $request->telefone,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'cep' => $request->cep,
            'rua' => $request->rua,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'uf' => $request->uf,
            'exige_nota_fiscal' => (bool) $request->input('exige_nota_fiscal', false),
            'observacoes' => $request->observacoes,
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
            return response()->json([
                'message' => 'Paciente atualizado com sucesso!'
            ]);
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
        $this->authorize('view', $paciente);

        $eventos = $this->gerarEventosDoPaciente($paciente);

        return view('pacientes.historico', compact('paciente', 'eventos'));
    }

    public function exportarHistoricoPdf(Paciente $paciente)
    {
        $this->authorize('exportarHistorico', $paciente);

        $eventos = $this->gerarEventosDoPaciente($paciente);

        AuditHelper::log('exported_historico', 'Exportou o histórico do paciente ' . $paciente->nome);

        $pdf = Pdf::loadView('pacientes.historico_pdf', compact('paciente', 'eventos'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("historico_{$paciente->nome}.pdf");
    }

    private function gerarEventosDoPaciente(Paciente $paciente): array
    {
        $sessoes = $paciente->sessoes()->orderBy('data_hora')->get();
        $evolucoes = $paciente->evolucoes()->orderBy('data')->get();

        $eventos = [];

        foreach ($sessoes as $sessao) {
            $data = Carbon::parse($sessao->data_hora);
            $eventos[] = [
                'tipo' => 'Sessão',
                'data' => $data->format('d/m/Y'),
                'hora' => $data->format('H:i'),
                'descricao' => 'Valor: R$ ' . number_format($sessao->valor, 2, ',', '.') .
                    ($sessao->foi_pago ? ' <span class="text-success">(Pago)</span>' : ' <span class="text-danger">(Pendente)</span>')
            ];
        }

        foreach ($evolucoes as $evolucao) {
            $data = Carbon::parse($evolucao->data);
            $descricao = trim($evolucao->texto ?? '');
            $tipo = $evolucao->tipo === 'medicacao' ? 'Medicação' : 'Evolução';
            $eventos[] = [
                'tipo' => $tipo,
                'data' => $data->format('d/m/Y'),
                'hora' => '',
                'descricao' => nl2br(e($descricao ?: 'Sem anotação registrada.'))
            ];
        }

        usort($eventos, function ($a, $b) {
            $dataHoraA = strtotime($a['data'] . ($a['hora'] ? ' ' . $a['hora'] : ' 00:00'));
            $dataHoraB = strtotime($b['data'] . ($b['hora'] ? ' ' . $b['hora'] : ' 00:00'));
            return $dataHoraA <=> $dataHoraB;
        });

        return $eventos;
    }

    public function aniversariantesHoje()
    {
        $hoje = Carbon::today();

        $pacientes = Paciente::where('user_id', auth()->id())
            ->whereMonth('data_nascimento', $hoje->month)
            ->whereDay('data_nascimento', $hoje->day)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nome' => $p->nome,
                    'idade' => Carbon::parse($p->data_nascimento)->age,
                ];
            });

        return response()->json($pacientes);
    }
}
