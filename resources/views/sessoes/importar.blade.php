@extends('layouts.app')

@section('title', 'Importar Sessões em Massa | PsiGestor')

@section('content')
<style>
    .imp-page {
        width: 100%;
    }

    .imp-content {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
    }

    .imp-header {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 22px;
    }

    .imp-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .imp-subtitle {
        color: #64748b;
        margin: 4px 0 0;
        font-size: .95rem;
    }

    .imp-btn {
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

    .imp-btn-outline {
        background: transparent;
        border: 2px solid #e2e8f0;
        color: #475569;
    }

    .imp-btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .imp-btn-primary {
        background: #2563eb;
        color: #fff;
    }

    .imp-btn-primary:hover {
        background: #1d4ed8;
    }

    .imp-btn-primary:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }

    .imp-btn-outline-primary {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .imp-btn-outline-primary:hover {
        background: #dbeafe;
    }

    .imp-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .imp-card {
        background: rgba(255,255,255,.98);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 22px;
        box-shadow: 0 14px 38px rgba(15,23,42,.06);
        padding: 24px;
    }

    .imp-step {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
    }

    .imp-step:last-child {
        margin-bottom: 0;
    }

    .imp-step-num {
        flex-shrink: 0;
        width: 38px;
        height: 38px;
        background: #eff6ff;
        color: #2563eb;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.1rem;
    }

    .imp-step-content {
        flex: 1;
    }

    .imp-step-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
    }

    .imp-step-desc {
        font-size: .95rem;
        color: #64748b;
        margin: 0 0 14px 0;
        line-height: 1.5;
    }

    .imp-drop-area {
        position: relative;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        background: #f8fafc;
        padding: 32px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 16px;
    }

    .imp-drop-area:hover, 
    .imp-drop-area.is-dragover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .imp-drop-icon {
        font-size: 2.5rem;
        color: #94a3b8;
        margin-bottom: 12px;
        transition: color 0.2s;
    }

    .imp-drop-area:hover .imp-drop-icon,
    .imp-drop-area.is-dragover .imp-drop-icon {
        color: #3b82f6;
    }

    .imp-drop-msg {
        display: block;
        color: #475569;
        font-size: .95rem;
        font-weight: 600;
    }

    .imp-drop-msg strong {
        color: #2563eb;
    }

    .imp-file-input {
        display: none;
    }

    .imp-inst-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .imp-inst-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .imp-inst-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .imp-inst-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .imp-inst-icon {
        color: #3b82f6;
        font-size: 1.1rem;
        margin-top: 2px;
    }

    .imp-inst-text {
        font-size: .9rem;
        color: #475569;
        line-height: 1.5;
    }

    .imp-inst-text strong {
        color: #0f172a;
        font-weight: 800;
    }

    @media (min-width: 768px) {
        .imp-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .imp-card {
            padding: 32px;
        }
    }

    @media (min-width: 992px) {
        .imp-grid {
            grid-template-columns: 1.5fr 1fr;
        }
    }
</style>

<div class="container-fluid py-4 imp-page">
    <div class="imp-content">
        
        <div class="imp-header">
            <div>
                <h1 class="imp-title">Importação de Sessões em Massa</h1>
                <p class="imp-subtitle">Envie sua planilha para adicionar múltiplos atendimentos de uma só vez.</p>
            </div>

            <a href="{{ route('sessoes.index') }}" class="imp-btn imp-btn-outline">
                <i class="bi bi-arrow-left"></i>
                Voltar para lista
            </a>
        </div>

        <div class="imp-grid">
            
            {{-- COLUNA ESQUERDA: AÇÕES --}}
            <div class="imp-card">
                
                {{-- PASSO 1 --}}
                <div class="imp-step">
                    <div class="imp-step-num">1</div>
                    <div class="imp-step-content">
                        <h2 class="imp-step-title">Baixe a planilha modelo</h2>
                        <p class="imp-step-desc">Utilize nosso modelo oficial para garantir que os dados sejam lidos perfeitamente pelo sistema.</p>
                        <a href="{{ route('sessoes.modelo') }}" class="imp-btn imp-btn-outline-primary no-spinner">
                            <i class="bi bi-file-earmark-arrow-down"></i> Baixar Modelo (.xlsx)
                        </a>
                    </div>
                </div>

                <hr class="text-muted opacity-25 my-4">

                {{-- PASSO 2 --}}
                <div class="imp-step">
                    <div class="imp-step-num">2</div>
                    <div class="imp-step-content">
                        <h2 class="imp-step-title">Envie o arquivo preenchido</h2>
                        <p class="imp-step-desc">Faça o upload da planilha com as sessões preenchidas nos formatos aceitos.</p>
                        
                        <form action="{{ route('sessoes.importar') }}" method="POST" enctype="multipart/form-data" id="import-form">
                            @csrf
                            
                            <div class="imp-drop-area" id="drop-area">
                                <i class="bi bi-cloud-arrow-up imp-drop-icon"></i>
                                <span class="imp-drop-msg">Arraste e solte o arquivo aqui, ou <strong>clique para selecionar</strong>.</span>
                                <input type="file" name="arquivo" id="arquivo" class="imp-file-input" required accept=".xlsx,.xls">
                            </div>

                            <button type="submit" class="imp-btn imp-btn-primary w-100" id="submit-button">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                                <span class="button-text"><i class="bi bi-upload"></i> Importar Sessões</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            {{-- COLUNA DIREITA: INSTRUÇÕES --}}
            <div class="imp-card" style="height: fit-content;">
                <div class="imp-inst-header">
                    <i class="bi bi-info-circle text-primary fs-5"></i>
                    <h3 class="imp-inst-title">Instruções de Preenchimento</h3>
                </div>

                <div class="imp-inst-list">
                    <div class="imp-inst-item">
                        <i class="bi bi-person imp-inst-icon"></i>
                        <div class="imp-inst-text">
                            <strong>Paciente:</strong> O nome deve estar idêntico ao cadastro no sistema (consulte a aba "Pacientes" na planilha modelo).
                        </div>
                    </div>

                    <div class="imp-inst-item">
                        <i class="bi bi-calendar3 imp-inst-icon"></i>
                        <div class="imp-inst-text">
                            <strong>Data e Hora:</strong> Utilize o formato exato <code>DD/MM/AAAA HH:MM</code>.
                        </div>
                    </div>

                    <div class="imp-inst-item">
                        <i class="bi bi-clock-history imp-inst-icon"></i>
                        <div class="imp-inst-text">
                            <strong>Duração:</strong> Insira apenas os números em minutos (Ex: <code>50</code>).
                        </div>
                    </div>

                    <div class="imp-inst-item">
                        <i class="bi bi-currency-dollar imp-inst-icon"></i>
                        <div class="imp-inst-text">
                            <strong>Valor:</strong> Digite utilizando vírgula para os centavos (Ex: <code>150,00</code>).
                        </div>
                    </div>

                    <div class="imp-inst-item">
                        <i class="bi bi-check-circle imp-inst-icon"></i>
                        <div class="imp-inst-text">
                            <strong>Foi Pago:</strong> Responda apenas com <code>Sim</code> ou <code>Não</code>.
                        </div>
                    </div>

                    <div class="imp-inst-item">
                        <i class="bi bi-bookmark-star imp-inst-icon"></i>
                        <div class="imp-inst-text">
                            <strong>Status:</strong> Use <code>Confirmada</code> para sessões que já aconteceram, ou <code>Pendente</code> para futuras.
                        </div>
                    </div>

                    <div class="imp-inst-item">
                        <i class="bi bi-journal-text imp-inst-icon"></i>
                        <div class="imp-inst-text">
                            <strong>Evolução:</strong> Texto de anotação clínica. É obrigatório caso a sessão já tenha ocorrido.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('arquivo');
    const dropArea = document.getElementById('drop-area');
    const fileMessage = dropArea.querySelector('.imp-drop-msg');
    const originalMessage = fileMessage.innerHTML;
    const importForm = document.getElementById('import-form');
    const submitButton = document.getElementById('submit-button');

    // Abre o seletor ao clicar na área
    dropArea.addEventListener('click', () => fileInput.click());

    // Previne comportamentos padrões de drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evtName => {
        dropArea.addEventListener(evtName, (e) => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    dropArea.addEventListener('dragover', () => {
        dropArea.classList.add('is-dragover');
    });

    dropArea.addEventListener('dragleave', () => {
        dropArea.classList.remove('is-dragover');
    });

    dropArea.addEventListener('drop', (e) => {
        dropArea.classList.remove('is-dragover');
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            updateFileMessage();
        }
    });

    fileInput.addEventListener('change', updateFileMessage);

    function updateFileMessage() {
        if (fileInput.files.length > 0) {
            fileMessage.innerHTML = `
                <i class="bi bi-file-earmark-excel-fill text-success fs-5 me-1"></i>
                <strong class="text-dark">Arquivo selecionado:</strong> <br>
                <span class="text-muted">${fileInput.files[0].name}</span>
            `;
        } else {
            fileMessage.innerHTML = originalMessage;
        }
    }

    if (importForm) {
        importForm.addEventListener('submit', function () {
            submitButton.disabled = true;
            submitButton.querySelector('.spinner-border').classList.remove('d-none');
            submitButton.querySelector('.button-text').textContent = 'Importando aguarde...';
        });
    }

    @if (session('resultado_importacao'))
        Swal.fire({
            title: 'Resultado da Importação',
            html: '<ul class="text-start m-0" style="padding-left:1.2rem; font-size: 0.95rem; color: #475569;">{!! implode("", array_map(fn($m) => "<li>{$m}</li>", session("resultado_importacao"))) !!}</ul>',
            icon: '{{ str_contains(implode(" ", session("resultado_importacao")), "❌") ? "error" : "success" }}',
            confirmButtonColor: '#2563eb',
            background: '#fff',
            color: '#0f172a'
        });
    @endif
});
</script>
@endsection