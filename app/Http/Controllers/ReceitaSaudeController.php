<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\ReceitaSaudeRecibo;
use App\Models\Sessao;
use App\Services\ReceitaSaude\ReceitaSaudeCsvService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReceitaSaudeController extends Controller
{
    public function __construct(private readonly ReceitaSaudeCsvService $csvService)
    {
    }

    public function index(Request $request)
    {
        $recibos = ReceitaSaudeRecibo::with(['paciente', 'sessao'])
            ->where('user_id', $request->user()->id)
            ->latest('data_pagamento')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $sessoesElegiveis = Sessao::where('user_id', $request->user()->id)
            ->where('foi_pago', true)
            ->where('moeda', 'BRL')
            ->whereNotNull('valor')
            ->where('valor', '>', 0)
            ->whereDoesntHave('receitaSaudeRecibo')
            ->count();

        $codigoOcupacao = $this->csvService->codigoOcupacaoParaUsuario($request->user());

        return view('receita-saude.index', compact('recibos', 'sessoesElegiveis', 'codigoOcupacao'));
    }

    public function sincronizar(Request $request)
    {
        $user = $request->user();
        $codigoOcupacao = $this->csvService->codigoOcupacaoParaUsuario($user);

        if (!$codigoOcupacao) {
            throw ValidationException::withMessages([
                'receita_saude' => 'No momento, a geração do Receita Saúde está disponível para Psicólogo(a) e Psiquiatra cadastrados no perfil.',
            ]);
        }

        $cpfProfissional = $this->csvService->cpfSomenteNumeros($user->cpf);

        if (strlen($cpfProfissional) !== 11) {
            throw ValidationException::withMessages([
                'receita_saude' => 'Complete o CPF do profissional no perfil antes de gerar recibos do Receita Saúde.',
            ]);
        }

        $sessoes = Sessao::with('paciente')
            ->where('user_id', $user->id)
            ->where('foi_pago', true)
            ->where('moeda', 'BRL')
            ->whereNotNull('valor')
            ->where('valor', '>', 0)
            ->whereDoesntHave('receitaSaudeRecibo')
            ->orderBy('data_hora')
            ->get();

        $criados = 0;
        $ignorados = 0;

        DB::transaction(function () use ($sessoes, $user, $codigoOcupacao, $cpfProfissional, &$criados, &$ignorados) {
            foreach ($sessoes as $sessao) {
                $paciente = $sessao->paciente;

                if (!$paciente) {
                    $ignorados++;
                    continue;
                }

                $cpfPaciente = $this->csvService->cpfSomenteNumeros($paciente->cpf);
                $dataSessao = $sessao->data_hora ? Carbon::parse($sessao->data_hora) : now();

                $recibo = ReceitaSaudeRecibo::create([
                    'user_id' => $user->id,
                    'paciente_id' => $paciente->id,
                    'sessao_id' => $sessao->id,
                    'data_pagamento' => $dataSessao->toDateString(),
                    'data_atendimento' => $dataSessao->toDateString(),
                    'codigo_rendimento' => ReceitaSaudeCsvService::CODIGO_RENDIMENTO_RECIBO,
                    'codigo_ocupacao' => $codigoOcupacao,
                    'valor_pagamento' => $sessao->valor,
                    'valor_deducao' => null,
                    'descricao' => null,
                    'recebido_de' => 'PF',
                    'cpf_pagador' => $cpfPaciente,
                    'cpf_beneficiario' => $cpfPaciente,
                    'indicador_cpf_nao_informado' => null,
                    'cnpj' => null,
                    'indicador_irrf' => null,
                    'valor_irrf' => null,
                    'indicador_recibo' => 'S',
                    'cpf_profissional' => $cpfProfissional,
                    'registro_profissional' => $this->csvService->registroProfissional($user->registro_profissional),
                    'status' => 'rascunho',
                ]);

                $recibo->descricao = $this->csvService->descricaoPadrao($recibo);
                $recibo->save();

                $criados++;
            }
        });

        AuditHelper::log('synced_receita_saude', "Gerou {$criados} rascunho(s) do Receita Saúde");

        $mensagem = "{$criados} rascunho(s) gerado(s) para o Receita Saúde.";
        if ($ignorados > 0) {
            $mensagem .= " {$ignorados} sessão(ões) foram ignoradas por falta de vínculo com paciente.";
        }

        return redirect()->route('receita-saude.index')->with('success', $mensagem);
    }

    public function exportar(Request $request)
    {
        $validated = $request->validate([
            'recibos' => ['required', 'array', 'min:1'],
            'recibos.*' => ['integer'],
        ]);

        $recibos = ReceitaSaudeRecibo::where('user_id', $request->user()->id)
            ->whereIn('id', $validated['recibos'])
            ->whereIn('status', ['rascunho', 'exportado'])
            ->orderBy('data_pagamento')
            ->orderBy('id')
            ->get();

        AuditHelper::log('exported_receita_saude_csv', 'Exportou CSV do Receita Saúde com ' . $recibos->count() . ' recibo(s)');

        return $this->csvService->downloadCsv($recibos);
    }

    public function atualizar(Request $request, ReceitaSaudeRecibo $recibo)
    {
        abort_unless($recibo->user_id === $request->user()->id, 403);

        $dados = $request->validate([
            'data_pagamento' => ['required', 'date'],
            'valor_pagamento' => ['required', 'numeric', 'min:0.01'],
            'cpf_pagador' => ['required', 'string', 'max:20'],
            'cpf_beneficiario' => ['required', 'string', 'max:20'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'numero_recibo' => ['nullable', 'string', 'max:80'],
            'status' => ['required', Rule::in(['rascunho', 'exportado', 'emitido', 'cancelado'])],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ]);

        $dados['cpf_pagador'] = $this->csvService->cpfSomenteNumeros($dados['cpf_pagador']);
        $dados['cpf_beneficiario'] = $this->csvService->cpfSomenteNumeros($dados['cpf_beneficiario']);
        $dados['descricao'] = mb_substr((string) ($dados['descricao'] ?? ''), 0, 255);

        if ($dados['status'] === 'emitido' && !$recibo->emitido_em) {
            $dados['emitido_em'] = now();
        }

        $recibo->update($dados);

        AuditHelper::log('updated_receita_saude_recibo', 'Atualizou recibo Receita Saúde ID ' . $recibo->id);

        return redirect()->route('receita-saude.index')->with('success', 'Recibo atualizado com sucesso.');
    }

    public function excluir(Request $request, ReceitaSaudeRecibo $recibo)
    {
        abort_unless($recibo->user_id === $request->user()->id, 403);

        if ($recibo->status === 'emitido') {
            return redirect()->route('receita-saude.index')
                ->with('error', 'Recibos marcados como emitidos não podem ser removidos do histórico. Marque como cancelado, se necessário.');
        }

        $recibo->delete();

        AuditHelper::log('deleted_receita_saude_recibo', 'Removeu recibo Receita Saúde ID ' . $recibo->id);

        return redirect()->route('receita-saude.index')->with('success', 'Rascunho removido.');
    }
}
