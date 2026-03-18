@extends('layouts.app')
@section('title', 'Criar Evolução | PsiGestor')

@section('content')
<div class="container">
    <h2 class="mb-3">Nova Evolução</h2>

    {{-- Botão Voltar --}}
    <a href="{{ route('evolucoes.index') }}" class="btn btn-outline-secondary mb-3 btn-voltar-sessoes">
        <i class="bi bi-arrow-left"></i> Voltar para lista de evoluções
    </a>

    {{-- Exibe erro via SweetAlert --}}
    @if(session('error'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro ao salvar',
                text: @json(session('error')),
                confirmButtonColor: '#00aaff'
            });
        </script>
    @endif

    @php
        $sessaoDt = null;
        if (isset($sessao) && $sessao && $sessao->data_hora) {
            $sessaoDt = $sessao->data_hora instanceof \Carbon\Carbon
                ? $sessao->data_hora
                : \Carbon\Carbon::parse($sessao->data_hora);
        }

        $valorDataForm = old(
            'data',
            $dataSelecionada
                ?? ($sessaoDt ? $sessaoDt->format('Y-m-d') : '')
        );
    @endphp

    <form action="{{ route('evolucoes.store') }}" method="POST" class="card p-3 shadow-sm">
        @csrf

        {{-- Paciente --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Paciente</label>
            <select id="pacienteSelect"
                    name="paciente_id"
                    class="form-select shadow-sm"
                    required
                    {{ (isset($sessao) && $sessao) ? 'disabled' : '' }}>
                <option value="">-- Selecione --</option>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}"
                        {{ (string)old('paciente_id', $pacienteSelecionado ?? ($sessao->paciente_id ?? '')) === (string)$paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
            @if(isset($sessao) && $sessao)
                <input type="hidden" name="paciente_id" value="{{ $sessao->paciente_id }}">
            @endif
        </div>

        {{-- Data --}}
        @if(isset($sessao) && $sessao)
            <input type="hidden" name="data" value="{{ $valorDataForm }}">
            <p class="text-muted">
                <strong>Data da Sessão:</strong>
                {{ $valorDataForm ? \Carbon\Carbon::parse($valorDataForm)->format('d/m/Y') : '—' }}
            </p>
        @else
            <div class="mb-3">
                <label class="form-label fw-semibold">Data</label>
                <input type="date"
                       name="data"
                       class="form-control shadow-sm"
                       value="{{ $valorDataForm }}"
                       required>
            </div>
        @endif

        {{-- Sessão vinculada --}}
        @if(isset($sessao) && $sessao)
            <input type="hidden" name="sessao_id" value="{{ $sessao->id }}">
            <p class="text-muted">
                Evolução vinculada automaticamente à sessão de
                <span class="badge bg-success">
                    {{ $sessaoDt ? $sessaoDt->format('d/m/Y H:i') : 'Sem data definida' }}
                </span>
            </p>
        @else
            <div class="mb-3">
                <label class="form-label fw-semibold">Sessão (opcional)</label>
                <select id="sessaoSelect" name="sessao_id" class="form-select shadow-sm">
                    <option value="">-- Selecione o paciente primeiro --</option>
                </select>
            </div>
        @endif

        <hr class="my-4">

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
        <div class="mb-4">
            <label class="form-label fw-semibold">Anotação Clínica</label>
            <textarea id="textoClinico" name="texto" class="form-control shadow-sm" rows="6" required>{{ old('texto') }}</textarea>
        </div>

        <hr class="my-4">

        {{-- Indicadores da Sessão --}}
        <div class="mb-2">
            <h5 class="mb-1">Indicadores da Sessão</h5>
            <p class="text-muted small mb-3">
                Registre marcadores clínicos desta evolução para acompanhar oscilações emocionais, intensidade e pontos de atenção.
            </p>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label for="estado_emocional" class="form-label fw-semibold">Estado emocional</label>
                <select name="estado_emocional" id="estado_emocional" class="form-select shadow-sm">
                    <option value="">Selecione</option>
                    <option value="estavel" {{ old('estado_emocional') == 'estavel' ? 'selected' : '' }}>Estável</option>
                    <option value="oscilante" {{ old('estado_emocional') == 'oscilante' ? 'selected' : '' }}>Oscilante</option>
                    <option value="ansioso" {{ old('estado_emocional') == 'ansioso' ? 'selected' : '' }}>Ansioso</option>
                    <option value="deprimido" {{ old('estado_emocional') == 'deprimido' ? 'selected' : '' }}>Deprimido</option>
                    <option value="irritavel" {{ old('estado_emocional') == 'irritavel' ? 'selected' : '' }}>Irritável</option>
                    <option value="apatico" {{ old('estado_emocional') == 'apatico' ? 'selected' : '' }}>Apático</option>
                    <option value="sobrecarregado" {{ old('estado_emocional') == 'sobrecarregado' ? 'selected' : '' }}>Sobrecarregado</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="intensidade" class="form-label fw-semibold">Intensidade emocional</label>
                <select name="intensidade" id="intensidade" class="form-select shadow-sm">
                    <option value="">Selecione</option>
                    <option value="1" {{ old('intensidade') == '1' ? 'selected' : '' }}>1 - Muito leve</option>
                    <option value="2" {{ old('intensidade') == '2' ? 'selected' : '' }}>2 - Leve</option>
                    <option value="3" {{ old('intensidade') == '3' ? 'selected' : '' }}>3 - Moderada</option>
                    <option value="4" {{ old('intensidade') == '4' ? 'selected' : '' }}>4 - Alta</option>
                    <option value="5" {{ old('intensidade') == '5' ? 'selected' : '' }}>5 - Muito alta</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="alerta" class="form-label fw-semibold">Alerta clínico</label>
                <select name="alerta" id="alerta" class="form-select shadow-sm">
                    <option value="">Selecione</option>
                    <option value="0" {{ old('alerta') === '0' ? 'selected' : '' }}>Sem alerta</option>
                    <option value="1" {{ old('alerta') === '1' ? 'selected' : '' }}>Atenção</option>
                    <option value="2" {{ old('alerta') === '2' ? 'selected' : '' }}>Ponto crítico</option>
                </select>
            </div>

            <div class="col-12">
                <label for="indicador_observacoes" class="form-label fw-semibold">Observações dos indicadores</label>
                <textarea
                    name="indicador_observacoes"
                    id="indicador_observacoes"
                    rows="3"
                    class="form-control shadow-sm"
                    placeholder="Ex.: paciente apresentou maior oscilação emocional nesta sessão, com relato de sobrecarga e aumento de ansiedade.">{{ old('indicador_observacoes') }}</textarea>
                <div class="form-text">
                    Campo opcional para complementar os marcadores clínicos desta evolução.
                </div>
            </div>
        </div>

        {{-- Botões --}}
        <div class="d-flex flex-column flex-md-row gap-2 mt-4">
            <button class="btn btn-success w-100 w-md-auto">Salvar</button>
            <a href="{{ route('evolucoes.index') }}" class="btn btn-secondary w-100 w-md-auto">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const topicosIA = document.getElementById('topicosIA');
    const gerarIA = document.getElementById('gerarIA');
    const copiarParaTexto = document.getElementById('copiarParaTexto');
    const loadingIA = document.getElementById('loadingIA');
    const previewIAWrapper = document.getElementById('previewIAWrapper');
    const previewIA = document.getElementById('previewIA');
    const textoClinico = document.getElementById('textoClinico');
    const indicadorObservacoes = document.getElementById('indicador_observacoes');

    let typingInterval = null;
    let isTyping = false;

    function stopTyping() {
        if (typingInterval) {
            clearInterval(typingInterval);
            typingInterval = null;
        }
        isTyping = false;
    }

    function autoResizeTextarea(el) {
        if (!el) return;
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    function typeTextInTextarea(element, text, speed = 14) {
        return new Promise((resolve) => {
            stopTyping();
            isTyping = true;
            element.value = '';
            element.focus();

            let index = 0;
            const total = text.length;

            typingInterval = setInterval(() => {
                let chunkSize = 1;

                const currentChar = text[index] || '';
                if (currentChar === ' ') {
                    chunkSize = 1;
                } else if (currentChar === '\n') {
                    chunkSize = 1;
                } else if (/[,.!?;:]/.test(currentChar)) {
                    chunkSize = 1;
                } else {
                    chunkSize = 2;
                }

                element.value += text.slice(index, index + chunkSize);
                index += chunkSize;

                element.scrollTop = element.scrollHeight;
                autoResizeTextarea(element);

                if (index >= total) {
                    stopTyping();
                    resolve();
                }
            }, speed);
        });
    }

    async function gerarEvolucaoComIA() {
        const topicos = (topicosIA?.value || '').trim();

        if (isTyping) {
            return;
        }

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
        autoResizeTextarea(previewIA);

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

            previewIAWrapper.style.display = 'block';
            previewIA.readOnly = true;

            await typeTextInTextarea(previewIA, data.evolucao.trim(), 12);

            previewIA.readOnly = false;
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
            stopTyping();
            previewIA.readOnly = false;

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

            if (isTyping) {
                return;
            }

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
            autoResizeTextarea(textoClinico);

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

    autoResizeTextarea(previewIA);
    autoResizeTextarea(textoClinico);
    autoResizeTextarea(indicadorObservacoes);

    previewIA.addEventListener('input', function () {
        autoResizeTextarea(previewIA);
    });

    textoClinico.addEventListener('input', function () {
        autoResizeTextarea(textoClinico);
    });

    if (indicadorObservacoes) {
        indicadorObservacoes.addEventListener('input', function () {
            autoResizeTextarea(indicadorObservacoes);
        });
    }

    @if(!isset($sessao) || !$sessao)
    const pacienteSelect = document.getElementById('pacienteSelect');
    const sessaoSelect   = document.getElementById('sessaoSelect');
    const selectedOld    = String(@json(old('sessao_id', '')) || '');

    if (!pacienteSelect || !sessaoSelect) return;

    function preencherSessoes(pacienteId) {
        sessaoSelect.innerHTML = '<option value="">Carregando sessões...</option>';
        sessaoSelect.disabled = true;

        if (!pacienteId) {
            sessaoSelect.innerHTML = '<option value="">-- Selecione o paciente primeiro --</option>';
            sessaoSelect.disabled = false;
            return;
        }

        fetch(`/pacientes/${encodeURIComponent(pacienteId)}/sessoes`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept':'application/json' }
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

            lista.forEach((s) => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.label || s.data_hora || 'Sem data / remarcar';
                if (String(s.id) === selectedOld) opt.selected = true;
                sessaoSelect.appendChild(opt);
            });
        })
        .catch(() => {
            sessaoSelect.innerHTML = '<option value="">Erro ao carregar sessões</option>';
        })
        .finally(() => {
            sessaoSelect.disabled = false;
        });
    }

    pacienteSelect.addEventListener('change', function () {
        preencherSessoes(this.value);
    });

    if (pacienteSelect.value) {
        preencherSessoes(pacienteSelect.value);
    }
    @endif
});
</script>
@endsection