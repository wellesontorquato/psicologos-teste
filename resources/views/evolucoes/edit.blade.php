@extends('layouts.app')

@section('title', 'Editar Evolução | PsiGestor')

@section('content')
<style>
    .evo-page {
        width: 100%;
    }

    .evo-content {
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
    }

    .evo-header {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 22px;
    }

    .evo-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .evo-subtitle {
        color: #64748b;
        margin: 4px 0 0;
        font-size: .95rem;
    }

    .evo-btn {
        min-height: 44px;
        border-radius: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
        padding: 0 20px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .evo-btn-outline {
        background: transparent;
        border: 2px solid #e2e8f0;
        color: #475569;
    }

    .evo-btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .evo-btn-primary {
        background: #2563eb;
        color: #fff;
    }

    .evo-btn-primary:hover {
        background: #1d4ed8;
    }

    .evo-btn-outline-primary {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .evo-btn-outline-primary:hover {
        background: #dbeafe;
    }

    .evo-btn-outline-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .evo-card {
        background: rgba(255,255,255,.98);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 22px;
        box-shadow: 0 14px 38px rgba(15,23,42,.06);
        padding: 24px;
        margin-bottom: 18px;
    }

    .evo-form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .evo-col-full {
        grid-column: 1 / -1;
    }

    .evo-indicators-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .evo-field label {
        font-size: .86rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        display: block;
    }

    .evo-field .form-control,
    .evo-field .form-select {
        min-height: 48px;
        border-radius: 14px;
        border-color: #dbe3ef;
        font-size: .95rem;
        color: #0f172a;
        background-color: #f8fafc;
        padding: 10px 16px;
        transition: all 0.2s ease;
    }

    .evo-field textarea.form-control {
        resize: vertical;
        line-height: 1.5;
    }

    .evo-field .form-control:focus,
    .evo-field .form-select:focus {
        border-color: #2563eb;
        background-color: #fff;
        box-shadow: 0 0 0 .25rem rgba(37,99,235,.12);
    }

    .evo-field-help {
        display: block;
        color: #64748b;
        font-size: .82rem;
        margin-top: 6px;
    }

    .evo-ai-box {
        background: linear-gradient(135deg, #f8fafc, #eff6ff);
        border: 1px solid #bfdbfe;
        border-radius: 18px;
        padding: 20px;
        margin-bottom: 18px;
    }

    .evo-ai-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        color: #1d4ed8;
        font-weight: 800;
    }

    .evo-ai-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin-top: 14px;
    }

    .evo-section-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
    }

    .evo-section-desc {
        color: #64748b;
        font-size: .9rem;
        margin: 0 0 16px 0;
    }

    .evo-form-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #e2e8f0;
    }

    .evo-badge-success { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: .8rem; }

    @media (min-width: 768px) {
        .evo-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .evo-card {
            padding: 32px;
        }

        .evo-form-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .evo-indicators-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .evo-form-actions {
            grid-template-columns: auto auto;
            justify-content: flex-end;
        }
    }
</style>

@php
    $sessaoDt = null;
    if ($evolucao->sessao && $evolucao->sessao->data_hora) {
        $sessaoDt = $evolucao->sessao->data_hora instanceof \Carbon\Carbon
            ? $evolucao->sessao->data_hora
            : \Carbon\Carbon::parse($evolucao->sessao->data_hora);
    }

    $indicador = $evolucao->indicador;
@endphp

<div class="container-fluid py-4 evo-page">
    <div class="evo-content">
        
        <div class="evo-header">
            <div>
                <h1 class="evo-title">Editar Evolução</h1>
                <p class="evo-subtitle">Atualize o registro clínico e o acompanhamento do paciente.</p>
            </div>

            <a href="{{ route('evolucoes.index') }}" class="evo-btn evo-btn-outline">
                <i class="bi bi-arrow-left"></i>
                Voltar para lista
            </a>
        </div>

        <form action="{{ route('evolucoes.update', $evolucao) }}" method="POST" class="evo-card">
            @csrf
            @method('PUT')

            <div class="evo-form-grid">
                
                {{-- Paciente --}}
                <div class="evo-field">
                    <label for="pacienteSelect">Paciente</label>
                    <select id="pacienteSelect"
                            name="paciente_id"
                            class="form-select"
                            required
                            {{ $evolucao->sessao ? 'disabled' : '' }}>
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
                <div class="evo-field">
                    @if($evolucao->sessao)
                        <input type="hidden" name="data" value="{{ old('data', \Carbon\Carbon::parse($evolucao->data)->format('Y-m-d')) }}">
                        <label>Data da Sessão</label>
                        <div class="form-control d-flex align-items-center bg-light text-muted">
                            <i class="bi bi-calendar3 me-2"></i>
                            {{ old('data', $evolucao->data) ? \Carbon\Carbon::parse(old('data', $evolucao->data))->format('d/m/Y') : '—' }}
                        </div>
                    @else
                        <label for="data">Data</label>
                        <input type="date"
                               id="data"
                               name="data"
                               class="form-control"
                               value="{{ old('data', \Carbon\Carbon::parse($evolucao->data)->format('Y-m-d')) }}"
                               required>
                    @endif
                </div>

                {{-- Sessão vinculada --}}
                <div class="evo-field evo-col-full">
                    @if($evolucao->sessao)
                        <input type="hidden" name="sessao_id" value="{{ $evolucao->sessao->id }}">
                        <label>Vínculo com Sessão</label>
                        <div class="d-flex align-items-center gap-2">
                            <span class="evo-badge-success">
                                <i class="bi bi-link-45deg"></i> Vinculada
                            </span>
                            <span class="text-muted small">
                                Sessão agendada para: <strong>{{ $sessaoDt ? $sessaoDt->format('d/m/Y H:i') : 'Sem data definida' }}</strong>
                            </span>
                        </div>
                    @else
                        <label for="sessaoSelect">Vincular a uma Sessão Agendada (Opcional)</label>
                        <select id="sessaoSelect" name="sessao_id" class="form-select">
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
                    @endif
                </div>

            </div>

            <hr class="text-muted opacity-25 my-4">

            {{-- Copiloto de IA --}}
            <div class="evo-ai-box evo-col-full">
                <div class="evo-ai-header">
                    <i class="bi bi-stars fs-5"></i>
                    <span>Copiloto de IA</span>
                </div>
                
                <div class="evo-field">
                    <label for="topicosIA">Tópicos da Sessão</label>
                    <textarea
                        id="topicosIA"
                        class="form-control"
                        rows="3"
                        placeholder="Ex.: ansiedade no trabalho, insônia há 3 dias, discussão com a mãe, acolhimento emocional...">{{ old('topicos_ia') }}</textarea>
                    <span class="evo-field-help">Escreva os tópicos principais e deixe a IA formular o texto profissional.</span>
                </div>

                <div class="evo-ai-actions">
                    <button type="button" id="gerarIA" class="evo-btn evo-btn-outline-primary">
                        <i class="bi bi-magic me-1"></i> Gerar evolução com IA
                    </button>

                    <button type="button" id="copiarParaTexto" class="evo-btn evo-btn-outline" style="display: none;">
                        <i class="bi bi-arrow-down-square me-1"></i> Usar texto gerado
                    </button>

                    <span id="loadingIA" class="text-muted small fw-bold ms-2" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Gerando anotação clínica...
                    </span>
                </div>

                {{-- Preview IA --}}
                <div class="evo-field mt-3" id="previewIAWrapper" style="display: none;">
                    <label for="previewIA" class="text-primary">Sugestão gerada pela IA</label>
                    <textarea id="previewIA" class="form-control" style="background: #fff; border-color: #bfdbfe;" rows="5"></textarea>
                    <span class="evo-field-help">Revise e ajuste o texto acima antes de movê-lo para a Anotação Clínica oficial.</span>
                </div>
            </div>

            {{-- Texto da Evolução --}}
            <div class="evo-field evo-col-full mb-4">
                <label for="textoClinico" style="font-size: 1rem;">Anotação Clínica Definitiva</label>
                <textarea id="textoClinico" name="texto" class="form-control" style="min-height: 150px;" required>{{ old('texto', $evolucao->texto) }}</textarea>
            </div>

            <hr class="text-muted opacity-25 my-4">

            {{-- Indicadores da Sessão --}}
            <div class="evo-col-full mb-3">
                <h3 class="evo-section-title">Indicadores da Sessão</h3>
                <p class="evo-section-desc">Atualize os marcadores clínicos desta evolução para acompanhar oscilações emocionais, intensidade e pontos de atenção.</p>
            </div>

            <div class="evo-indicators-grid">
                <div class="evo-field">
                    <label for="estado_emocional">Estado emocional</label>
                    <select name="estado_emocional" id="estado_emocional" class="form-select">
                        <option value="">Selecione</option>
                        <option value="estavel" {{ old('estado_emocional', $indicador->estado_emocional ?? '') == 'estavel' ? 'selected' : '' }}>Estável</option>
                        <option value="oscilante" {{ old('estado_emocional', $indicador->estado_emocional ?? '') == 'oscilante' ? 'selected' : '' }}>Oscilante</option>
                        <option value="ansioso" {{ old('estado_emocional', $indicador->estado_emocional ?? '') == 'ansioso' ? 'selected' : '' }}>Ansioso</option>
                        <option value="deprimido" {{ old('estado_emocional', $indicador->estado_emocional ?? '') == 'deprimido' ? 'selected' : '' }}>Deprimido</option>
                        <option value="irritavel" {{ old('estado_emocional', $indicador->estado_emocional ?? '') == 'irritavel' ? 'selected' : '' }}>Irritável</option>
                        <option value="apatico" {{ old('estado_emocional', $indicador->estado_emocional ?? '') == 'apatico' ? 'selected' : '' }}>Apático</option>
                        <option value="sobrecarregado" {{ old('estado_emocional', $indicador->estado_emocional ?? '') == 'sobrecarregado' ? 'selected' : '' }}>Sobrecarregado</option>
                    </select>
                </div>

                <div class="evo-field">
                    <label for="intensidade">Intensidade emocional</label>
                    <select name="intensidade" id="intensidade" class="form-select">
                        <option value="">Selecione</option>
                        <option value="1" {{ (string)old('intensidade', $indicador->intensidade ?? '') === '1' ? 'selected' : '' }}>1 - Muito leve</option>
                        <option value="2" {{ (string)old('intensidade', $indicador->intensidade ?? '') === '2' ? 'selected' : '' }}>2 - Leve</option>
                        <option value="3" {{ (string)old('intensidade', $indicador->intensidade ?? '') === '3' ? 'selected' : '' }}>3 - Moderada</option>
                        <option value="4" {{ (string)old('intensidade', $indicador->intensidade ?? '') === '4' ? 'selected' : '' }}>4 - Alta</option>
                        <option value="5" {{ (string)old('intensidade', $indicador->intensidade ?? '') === '5' ? 'selected' : '' }}>5 - Muito alta</option>
                    </select>
                </div>

                <div class="evo-field">
                    <label for="alerta">Alerta clínico</label>
                    <select name="alerta" id="alerta" class="form-select">
                        <option value="">Selecione</option>
                        <option value="0" {{ (string)old('alerta', isset($indicador->alerta) ? $indicador->alerta : '') === '0' ? 'selected' : '' }}>Sem alerta</option>
                        <option value="1" {{ (string)old('alerta', $indicador->alerta ?? '') === '1' ? 'selected' : '' }}>Atenção</option>
                        <option value="2" {{ (string)old('alerta', $indicador->alerta ?? '') === '2' ? 'selected' : '' }}>Ponto crítico</option>
                    </select>
                </div>

                <div class="evo-field evo-col-full">
                    <label for="indicador_observacoes">Observações dos indicadores</label>
                    <textarea
                        name="indicador_observacoes"
                        id="indicador_observacoes"
                        rows="2"
                        class="form-control"
                        placeholder="Ex.: paciente apresentou maior oscilação emocional nesta sessão, com relato de sobrecarga e aumento de ansiedade.">{{ old('indicador_observacoes', $indicador->observacoes ?? '') }}</textarea>
                    <span class="evo-field-help">Campo opcional para complementar os marcadores clínicos desta evolução.</span>
                </div>
            </div>

            <div class="evo-form-actions">
                <a href="{{ route('evolucoes.index') }}" class="evo-btn evo-btn-outline">Cancelar</a>
                <button type="submit" class="evo-btn evo-btn-primary">
                    <i class="bi bi-arrow-repeat"></i>
                    Atualizar Evolução
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erro ao salvar',
            text: @json(session('error')),
            confirmButtonColor: '#2563eb',
            background: '#fff',
            color: '#0f172a'
        });
    @endif

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
                if (currentChar === ' ' || currentChar === '\n' || /[,.!?;:]/.test(currentChar)) {
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
            Swal.fire({
                icon: 'warning',
                title: 'Tópicos não informados',
                text: 'Digite alguns tópicos da sessão para gerar a evolução com IA.',
                confirmButtonColor: '#2563eb'
            });
            return;
        }

        gerarIA.disabled = true;
        copiarParaTexto.style.display = 'none';
        loadingIA.style.display = 'inline-flex';
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
                body: JSON.stringify({ topicos: topicos })
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
            copiarParaTexto.style.display = 'inline-flex';

            Swal.fire({
                icon: 'success',
                title: 'Evolução gerada',
                text: 'Revise o texto e clique em "Usar texto gerado" para enviar à anotação clínica.',
                confirmButtonColor: '#2563eb',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000
            });
        } catch (error) {
            console.error(error);
            stopTyping();
            previewIA.readOnly = false;

            Swal.fire({
                icon: 'error',
                title: 'Erro ao gerar evolução',
                text: error.message || 'Ocorreu um erro ao comunicar com a IA.',
                confirmButtonColor: '#2563eb'
            });
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
                Swal.fire({
                    icon: 'warning',
                    title: 'Nada para copiar',
                    text: 'Gere ou edite um texto antes de usar esta opção.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            textoClinico.value = textoGerado;
            textoClinico.focus();
            autoResizeTextarea(textoClinico);

            Swal.fire({
                icon: 'success',
                title: 'Texto aplicado',
                text: 'A sugestão da IA foi enviada para a anotação clínica.',
                confirmButtonColor: '#2563eb',
                timer: 2000,
                showConfirmButton: false
            });
        });
    }

    // Auto-resize de textareas on load e input
    autoResizeTextarea(previewIA);
    autoResizeTextarea(textoClinico);
    autoResizeTextarea(indicadorObservacoes);

    [previewIA, textoClinico, indicadorObservacoes].forEach(el => {
        if(el) {
            el.addEventListener('input', () => autoResizeTextarea(el));
        }
    });

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