@extends('layouts.app')

@section('title', 'Importar Sessões em Massa | PsiGestor')

@section('content')
<div class="container my-4 my-md-5">

    {{-- CABEÇALHO DA PÁGINA --}}
    <div class="page-header text-center text-lg-start mb-4">
        <h2 class="display-6 fw-bold">Importação de Sessões em Massa</h2>
        <p class="text-muted lead">Envie sua planilha do Excel para adicionar múltiplas sessões de uma só vez.</p>
    </div>

    <div class="row g-4 g-lg-5">

        {{-- COLUNA DA ESQUERDA (AÇÃO PRINCIPAL) --}}
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm border-light">
                <div class="card-body p-4 p-lg-5">
                    
                    {{-- PASSO 1 --}}
                    <div class="step-box mb-4">
                        <span class="step-number">1</span>
                        <div>
                            <h5 class="mb-1">Baixe a planilha modelo</h5>
                            <p class="text-muted mb-2">Use nosso modelo para garantir que os dados estejam no formato correto.</p>
                            <a href="{{ route('sessoes.modelo') }}" class="btn btn-outline-primary no-spinner">
                                <i class="bi bi-download me-2"></i> Baixar Modelo (.xlsx)
                            </a>
                        </div>
                    </div>

                    {{-- PASSO 2 --}}
                    <div class="step-box">
                        <span class="step-number">2</span>
                        <div>
                            <h5 class="mb-1">Envie o arquivo preenchido</h5>
                            <p class="text-muted mb-3">Arraste e solte o arquivo na área abaixo ou clique para selecionar.</p>
                            
                            <form action="{{ route('sessoes.importar') }}" method="POST" enctype="multipart/form-data" id="import-form">
                                @csrf
                                <div class="file-drop-area mb-3">
                                    <i class="bi bi-cloud-arrow-up display-4"></i>
                                    <span class="file-message">Arraste e solte o arquivo aqui, ou <strong>clique para selecionar</strong>.</span>
                                    <input type="file" name="arquivo" id="arquivo" class="file-input" required accept=".xlsx,.xls">
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100" id="submit-button">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span class="button-text">Importar Sessões</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUNA DA DIREITA (INSTRUÇÕES) --}}
        <div class="col-lg-5">
            <div class="card h-100 shadow-sm border-light">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Instruções de Preenchimento</h5>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <i class="bi bi-person"></i>
                            <div>
                                <strong>Paciente:</strong> Nome idêntico ao cadastro no sistema.
                            </div>
                        </div>
                        <div class="list-group-item">
                            <i class="bi bi-calendar-event"></i>
                            <div>
                                <strong>Data:</strong> Formato `DD/MM/AAAA HH:MM`.
                            </div>
                        </div>
                        <div class="list-group-item">
                            <i class="bi bi-clock"></i>
                            <div>
                                <strong>Duração (Minutos):</strong> Apenas números (ex: `50`).
                            </div>
                        </div>
                         <div class="list-group-item">
                            <i class="bi bi-cash-coin"></i>
                            <div>
                                <strong>Valor:</strong> Formato com vírgula (ex: `150,00`).
                            </div>
                        </div>
                        <div class="list-group-item">
                            <i class="bi bi-check2-circle"></i>
                            <div>
                                <strong>Pago:</strong> Use `Sim` ou `Não`.
                            </div>
                        </div>
                        <div class="list-group-item">
                            <i class="bi bi-bookmark-star"></i>
                            <div>
                                <strong>Status:</strong> `Confirmada` ou `Pendente`.
                            </div>
                        </div>
                        <div class="list-group-item">
                            <i class="bi bi-file-text"></i>
                            <div>
                                <strong>Evolução:</strong> Texto livre (obrigatório se a sessão já ocorreu).
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ---------------------------------- */
    /* ESTILOS BASE (MOBILE-FIRST) E TEMA */
    /* ---------------------------------- */
    :root {
        --psi-primary: #0077ff;
        --psi-secondary: #00aaff;
        --psi-light-bg: #f0f8ff;
        --psi-text-muted: #6c757d;
    }

    .page-header h2 {
        color: var(--psi-primary);
    }

    .step-box {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    .step-number {
        flex-shrink: 0;
        width: 35px;
        height: 35px;
        background-color: var(--psi-light-bg);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: var(--psi-primary);
        font-size: 1.1rem;
    }

    .file-drop-area {
        position: relative;
        border: 2px dashed #d0d0d0;
        border-radius: .5rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        background-color: #fdfdfd;
        transition: border-color .3s, background-color .3s;
    }
    .file-drop-area i {
        color: #ced4da;
    }
    .file-drop-area:hover, .file-drop-area.is-dragover {
        border-color: var(--psi-primary);
        background-color: var(--psi-light-bg);
    }
    .file-input {
        position: absolute; left: 0; top: 0; height: 100%; width: 100%; cursor: pointer; opacity: 0;
    }
    .file-message {
        display: block; margin-top: 0.5rem; color: var(--psi-text-muted); font-size: 0.9rem;
    }
    
    .btn-primary {
        background-color: var(--psi-primary);
        border-color: var(--psi-primary);
    }
    .btn-primary:hover {
        background-color: #005fcc; /* Um pouco mais escuro no hover */
        border-color: #005fcc;
    }
    .btn-outline-primary {
        color: var(--psi-primary);
        border-color: var(--psi-primary);
    }
    .btn-outline-primary:hover {
        background-color: var(--psi-primary);
        color: white;
    }
    
    .list-group-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0;
        background: none;
        border: none;
        border-bottom: 1px solid #f0f0f0;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
    .list-group-item i {
        margin-top: 2px;
        color: var(--psi-primary);
    }
    
    /* ------------------------------------------------------------------ */
    /* MELHORIAS PARA DESKTOPS (LG e maiores - 992px ou mais) */
    /* ------------------------------------------------------------------ */
    @media (min-width: 992px) {
        .step-box {
            gap: 1.5rem;
        }
        .step-number {
            width: 40px;
            height: 40px;
        }
        .file-drop-area {
            padding: 2.5rem;
        }
        .file-message {
            font-size: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('arquivo');
    const dropArea = fileInput.closest('.file-drop-area');
    const fileMessage = dropArea.querySelector('.file-message');
    const originalMessage = fileMessage.innerHTML;
    const importForm = document.getElementById('import-form');
    const submitButton = document.getElementById('submit-button');

    dropArea.addEventListener('dragover', (e) => { e.preventDefault(); dropArea.classList.add('is-dragover'); });
    dropArea.addEventListener('dragleave', () => { dropArea.classList.remove('is-dragover'); });
    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        dropArea.classList.remove('is-dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            updateFileMessage();
        }
    });
    fileInput.addEventListener('change', updateFileMessage);

    function updateFileMessage() {
        if (fileInput.files.length > 0) {
            fileMessage.innerHTML = `<i class="bi bi-file-earmark-check-fill text-success"></i> <strong>Arquivo selecionado:</strong> ${fileInput.files[0].name}`;
        } else {
            fileMessage.innerHTML = originalMessage;
        }
    }
    if(importForm) {
        importForm.addEventListener('submit', function() {
            submitButton.disabled = true;
            submitButton.querySelector('.spinner-border').classList.remove('d-none');
            submitButton.querySelector('.button-text').textContent = 'Importando...';
        });
    }
    @if (session('sucesso'))
        Swal.fire({
            title: 'Sucesso!',
            text: '{{ session('sucesso') }}',
            icon: 'success',
            confirmButtonColor: 'var(--psi-primary)'
        });
    @endif
});
</script>
@endpush