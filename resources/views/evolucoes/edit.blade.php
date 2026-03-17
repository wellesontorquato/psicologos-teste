@extends('layouts.app')
@section('title', 'Editar Evolução | PsiGestor')

@section('content')
<div class="container">
    <h2 class="mb-3">Editar Evolução</h2>

    {{-- Botão Voltar --}}
    <a href="{{ route('evolucoes.index') }}" class="btn btn-outline-secondary mb-3 btn-voltar-sessoes">
        <i class="bi bi-arrow-left"></i> Voltar para lista de evoluções
    </a>

    {{-- Formulário --}}
    <form action="{{ route('evolucoes.update', $evolucao) }}" method="POST" class="card p-3 shadow-sm">
        @csrf
        @method('PUT')

        {{-- Paciente --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Paciente</label>
            <select id="pacienteSelect"
                    name="paciente_id"
                    class="form-select shadow-sm"
                    required
                    {{ $evolucao->sessao ? 'readonly disabled' : '' }}>
                <option value="">-- Selecione --</option>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}"
                        {{ (string)old('paciente_id', $evolucao->paciente_id) === (string)$paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
            @if($evolucao->sessao)
                <input type="hidden" name="paciente_id" value="{{ old('paciente_id', $evolucao->paciente_id) }}">
            @endif
        </div>

        {{-- Data --}}
        @if($evolucao->sessao)
            <input type="hidden" name="data" value="{{ old('data', \Carbon\Carbon::parse($evolucao->data)->format('Y-m-d')) }}">
            <p class="text-muted">
                <strong>Data da Sessão:</strong>
                {{ old('data', \Carbon\Carbon::parse($evolucao->data)->format('Y-m-d')) ? \Carbon\Carbon::parse(old('data', $evolucao->data))->format('d/m/Y') : '—' }}
            </p>
        @else
            <div class="mb-3">
                <label class="form-label fw-semibold">Data</label>
                <input type="date"
                       name="data"
                       class="form-control shadow-sm"
                       value="{{ old('data', \Carbon\Carbon::parse($evolucao->data)->format('Y-m-d')) }}"
                       required>
            </div>
        @endif

        {{-- Sessão vinculada --}}
        @php
            $sessaoDt = null;
            if ($evolucao->sessao && $evolucao->sessao->data_hora) {
                $sessaoDt = $evolucao->sessao->data_hora instanceof \Carbon\Carbon
                    ? $evolucao->sessao->data_hora
                    : \Carbon\Carbon::parse($evolucao->sessao->data_hora);
            }
        @endphp

        @if($evolucao->sessao)
            <input type="hidden" name="sessao_id" value="{{ $evolucao->sessao->id }}">
            <p class="text-muted">
                Evolução vinculada à sessão de
                <strong>{{ $sessaoDt ? $sessaoDt->format('d/m/Y H:i') : 'Sem data definida' }}</strong>.
            </p>
        @else
            <div class="mb-3">
                <label class="form-label fw-semibold">Sessão (opcional)</label>
                <select id="sessaoSelect" name="sessao_id" class="form-select shadow-sm">
                    <option value="">-- Selecionar sessão --</option>
                    @foreach($sessoesPaciente as $s)
                        @php
                            $labelData = optional($s->data_hora)?->format('d/m/Y H:i');
                            if (!$labelData && $s->data_hora) {
                                $labelData = \Illuminate\Support\Carbon::parse($s->data_hora)->format('d/m/Y H:i');
                            }
                            $label = $labelData
                                ? $labelData . ' (' . (int)($s->duracao ?? 0) . 'min)'
                                : 'Sem data / remarcar';
                        @endphp
                        <option value="{{ $s->id }}"
                            {{ (string)old('sessao_id', $evolucao->sessao_id) === (string)$s->id ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- Copiloto de IA --}}
        <div class="mb-3">
            <label for="topicosIA" class="form-label fw-semibold">Tópicos da Sessão (opcional)</label>
            <textarea
                id="topicosIA"
                class="form-control shadow-sm"
                rows="4"
                placeholder="Ex.: ansiedade no trabalho, insônia há 3 dias, discussão com a mãe, acolhimento emocional, identificação de gatilhos, combinado registro de pensamentos...">{{ old('topicos_ia') }}</textarea>
            <div class="form-text">
                Escreva tópicos curtos da sessão e clique em <strong>Gerar evolução com IA</strong>.
            </div>
        </div>

        <div class="mb-3 d-flex flex-column flex-md-row gap-2 align-items-stretch align-items-md-center">
            <button type="button" id="gerarIA" class="btn btn-outline-primary">
                <i class="bi bi-stars me-1"></i> Gerar evolução com IA
            </button>

            <button type="button" id="copiarParaTexto" class="btn btn-outline-secondary" style="display: none;">
                <i class="bi bi-arrow-down-square me-1"></i> Usar texto gerado
            </button>

            <span id="loadingIA" class="text-muted small" style="display: none;">
                Gerando evolução...
            </span>
        </div>

        {{-- Preview IA --}}
        <div class="mb-3" id="previewIAWrapper" style="display: none;">
            <label for="previewIA" class="form-label fw-semibold">Sugestão gerada pela IA</label>
            <textarea id="previewIA" class="form-control shadow-sm" rows="6"></textarea>
            <div class="form-text">
                Você pode editar o texto acima antes de enviá-lo para a anotação clínica.
            </div>
        </div>

        {{-- Texto da Evolução --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Anotação Clínica</label>
            <textarea id="textoClinico" name="texto" class="form-control shadow-sm" rows="6" required>{{ old('texto', $evolucao->texto) }}</textarea>
        </div>

        {{-- Botões responsivos --}}
        <div class="d-flex flex-column flex-md-row gap-2">
            <button type="submit" class="btn btn-primary w-100 w-md-auto">Atualizar</button>
            <a href="{{ route('evolucoes.index') }}" class="btn btn-secondary w-100 w-md-auto">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const topicosIA = document.getElementById('topicosIA');
    const gerarIA = document.getElementById('gerarIA');
    const copiarParaTexto = document.getElementById('copiarParaTexto');
    const loadingIA = document.getElementById('loadingIA');
    const previewIAWrapper = document.getElementById('previewIAWrapper');
    const previewIA = document.getElementById('previewIA');
    const textoClinico = document.getElementById('textoClinico');

    async function gerarEvolucaoComIA() {
        const topicos = (topicosIA?.value || '').trim();

        if (!topicos) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tópicos não informados',
                    text: 'Digite alguns tópicos da sessão para gerar a evolução com IA.',
                    confirmButtonColor: '#00aaff'
                });
            } else {
                alert('Digite alguns tópicos da sessão para gerar a evolução com IA.');
            }
            return;
        }

        gerarIA.disabled = true;
        copiarParaTexto.style.display = 'none';
        loadingIA.style.display = 'inline';
        previewIAWrapper.style.display = 'none';
        previewIA.value = '';

        try {
            const response = await fetch("{{ route('evolucoes.gerarIA') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    topicos: topicos
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data?.message || 'Não foi possível gerar a evolução.');
            }

            if (!data.evolucao || typeof data.evolucao !== 'string') {
                throw new Error('A IA não retornou uma evolução válida.');
            }

            previewIA.value = data.evolucao.trim();
            previewIAWrapper.style.display = 'block';
            copiarParaTexto.style.display = 'inline-block';

            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Evolução gerada',
                    text: 'Revise o texto e clique em "Usar texto gerado" para enviar à anotação clínica.',
                    confirmButtonColor: '#00aaff'
                });
            }
        } catch (error) {
            console.error(error);

            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao gerar evolução',
                    text: error.message || 'Ocorreu um erro ao comunicar com a IA.',
                    confirmButtonColor: '#00aaff'
                });
            } else {
                alert(error.message || 'Ocorreu um erro ao comunicar com a IA.');
            }
        } finally {
            gerarIA.disabled = false;
            loadingIA.style.display = 'none';
        }
    }

    if (gerarIA) {
        gerarIA.addEventListener('click', gerarEvolucaoComIA);
    }

    if (copiarParaTexto) {
        copiarParaTexto.addEventListener('click', function () {
            const textoGerado = (previewIA?.value || '').trim();

            if (!textoGerado) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nada para copiar',
                        text: 'Gere ou edite um texto antes de usar esta opção.',
                        confirmButtonColor: '#00aaff'
                    });
                } else {
                    alert('Gere ou edite um texto antes de usar esta opção.');
                }
                return;
            }

            textoClinico.value = textoGerado;
            textoClinico.focus();

            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Texto aplicado',
                    text: 'A sugestão da IA foi enviada para a anotação clínica.',
                    confirmButtonColor: '#00aaff',
                    timer: 1800,
                    showConfirmButton: false
                });
            }
        });
    }

    const pacienteSelect = document.getElementById('pacienteSelect');
    const sessaoSelect   = document.getElementById('sessaoSelect');

    @if(!$evolucao->sessao)
    if (pacienteSelect && sessaoSelect) {
        pacienteSelect.addEventListener('change', function () {
            const pacienteId = this.value;
            sessaoSelect.innerHTML = '<option value="">Carregando sessões...</option>';

            if (!pacienteId) {
                sessaoSelect.innerHTML = '<option value="">-- Selecione o paciente --</option>';
                return;
            }

            fetch(`/pacientes/${encodeURIComponent(pacienteId)}/sessoes`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async (res) => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then((lista) => {
                sessaoSelect.innerHTML = '<option value="">-- Sem vínculo --</option>';

                if (!Array.isArray(lista) || lista.length === 0) {
                    sessaoSelect.insertAdjacentHTML('beforeend', '<option value="">Nenhuma sessão encontrada</option>');
                    return;
                }

                const selecionada = String(@json(old('sessao_id', $evolucao->sessao_id)) || '');

                lista.forEach((s) => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.label || s.data_hora || 'Sem data / remarcar';
                    if (String(s.id) === selecionada) opt.selected = true;
                    sessaoSelect.appendChild(opt);
                });
            })
            .catch(() => {
                sessaoSelect.innerHTML = '<option value="">Erro ao carregar sessões</option>';
            });
        });

        if (pacienteSelect.value) {
            pacienteSelect.dispatchEvent(new Event('change'));
        }
    }
    @endif
});
</script>
@endsection