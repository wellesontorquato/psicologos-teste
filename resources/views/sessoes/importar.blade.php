Com certeza\! É uma ótima prática e fico feliz em refatorar o código para seguir a metodologia "mobile-first".

A principal diferença, como você verá, está na estrutura do CSS. Agora, os estilos definidos primeiro são os mais simples, para telas pequenas. Depois, usamos uma `media query` com `min-width` para adicionar os detalhes e espaçamentos maiores para telas de tablets e desktops.

O resultado visual será muito parecido com o anterior (pois o design já era bem adaptável), mas a estrutura do seu CSS agora está mais robusta e segue a convenção mobile-first.

### Página `importar.blade.php` com CSS Mobile-First

Substitua o conteúdo do seu arquivo por este código:

```php
@extends('layouts.app')

@section('title', 'Importar Sessões em Massa | PsiGestor')

@section('content')
<div class="container my-4 my-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm border-light">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0"><i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Importar Sessões por Planilha</h4>
                </div>
                <div class="card-body p-3 p-md-4 p-lg-5">

                    @if (session('sucesso'))
                        {{-- O SweetAlert já está no seu layout, então não precisa do script aqui --}}
                    @endif
                    
                    {{-- PASSO 1 --}}
                    <div class="step-box mb-4">
                        <span class="step-number">1</span>
                        <div>
                            <h5 class="mb-1">Baixe a planilha modelo</h5>
                            <p class="text-muted mb-2">Preencha com os dados das sessões para garantir que a importação funcione corretamente.</p>
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
                                    <i class="bi bi-cloud-arrow-up display-4 text-secondary"></i>
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

            {{-- INSTRUÇÕES --}}
            <div class="card shadow-sm border-light mt-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Instruções de Preenchimento</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <p class="text-muted">Sua planilha deve conter as seguintes colunas para uma importação bem-sucedida:</p>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center">
                            <i class="bi bi-person-fill text-primary me-3 fs-4"></i>
                            <div>
                                <strong class="d-block">Paciente</strong>
                                Nome completo, idêntico ao cadastrado no sistema, e que aparece na aba Pacientes da planilha modelo.
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="bi bi-calendar-event-fill text-success me-3 fs-4"></i>
                            <div>
                                <strong class="d-block">Data</strong>
                                Formato: `DD/MM/AAAA HH:MM` (ex: `07/08/2025 10:00`).
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="bi bi-clock-fill text-info me-3 fs-4"></i>
                            <div>
                                <strong class="d-block">Duração (Minutos)</strong>
                                Apenas o número (ex: `50`).
                            </div>
                        </div>
                         <div class="list-group-item d-flex align-items-center">
                            <i class="bi bi-cash-coin text-warning me-3 fs-4"></i>
                            <div>
                                <strong class="d-block">Valor</strong>
                                Formato com vírgula (ex: `150,00`).
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                            <div>
                                <strong class="d-block">Pago</strong>
                                Apenas `Sim` ou `Não`.
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="bi bi-bookmark-star-fill text-info me-3 fs-4"></i>
                            <div>
                                <strong class="d-block">Status</strong>
                                Opções: `Confirmada` ou `Pendente`.
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <i class="bi bi-file-text-fill text-secondary me-3 fs-4"></i>
                            <div>
                                <strong class="d-block">Evolução</strong>
                                Campo opcional, mas obrigatório se a sessão já aconteceu.
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
    /* ------------------------- */
    /* ESTILOS BASE (MOBILE-FIRST) */
    /* ------------------------- */
    /* Estilos aplicados em todas as telas, começando pela menor. */

    .card-header h4, .card-header h5 {
        font-weight: 500;
        font-size: 1.1rem; /* Um pouco menor no mobile */
    }
    .step-box {
        display: flex;
        align-items: flex-start;
        gap: 1rem; /* Espaçamento menor para mobile */
    }
    .step-number {
        flex-shrink: 0;
        width: 35px;
        height: 35px;
        background-color: #e9ecef;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #495057;
        font-size: 1.1rem;
    }
    .file-drop-area {
        position: relative;
        border: 2px dashed #adb5bd;
        border-radius: .5rem;
        padding: 1.5rem; /* Padding menor para mobile */
        text-align: center;
        cursor: pointer;
        transition: border-color .3s, background-color .3s;
    }
    .file-input {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 100%;
        cursor: pointer;
        opacity: 0;
    }
    .file-message {
        display: block;
        margin-top: 0.5rem;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .file-drop-area:hover, .file-drop-area.is-dragover {
        border-color: var(--bs-primary);
        background-color: #f8f9fa;
    }
    .list-group-item {
        padding: 0.9rem 0; /* Padding vertical menor */
        border-bottom: 1px solid #eee !important;
    }
    .list-group-item:last-child {
        border-bottom: none !important;
    }

    /* ----------------------------------------------------------------- */
    /* MELHORIAS PARA TELAS MAIORES (TABLETS E DESKTOPS - 768px ou mais) */
    /* ----------------------------------------------------------------- */
    @media (min-width: 768px) {
        .card-header h4, .card-header h5 {
            font-size: 1.25rem; /* Retorna ao tamanho original */
        }
        .step-box {
            gap: 1.5rem; /* Aumenta o espaçamento */
        }
        .step-number {
            width: 40px;
            height: 40px;
        }
        .file-drop-area {
            padding: 2.5rem; /* Aumenta o padding */
        }
        .file-message {
            margin-top: 1rem;
            font-size: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
{{-- O JavaScript não precisa de alterações, pois é baseado em eventos e não em layout --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('arquivo');
    const dropArea = fileInput.closest('.file-drop-area');
    const fileMessage = dropArea.querySelector('.file-message');
    const originalMessage = fileMessage.innerHTML;
    const importForm = document.getElementById('import-form');
    const submitButton = document.getElementById('submit-button');

    // Highlight drop area when file is dragged over
    dropArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropArea.classList.add('is-dragover');
    });

    dropArea.addEventListener('dragleave', () => {
        dropArea.classList.remove('is-dragover');
    });

    // Handle dropped file
    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        dropArea.classList.remove('is-dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            updateFileMessage();
        }
    });

    // Handle file selected via click
    fileInput.addEventListener('change', updateFileMessage);

    function updateFileMessage() {
        if (fileInput.files.length > 0) {
            fileMessage.innerHTML = `<i class="bi bi-file-earmark-check-fill text-success"></i> <strong>Arquivo selecionado:</strong> ${fileInput.files[0].name}`;
        } else {
            fileMessage.innerHTML = originalMessage;
        }
    }

    // Handle form submission spinner
    if(importForm) {
        importForm.addEventListener('submit', function() {
            submitButton.disabled = true;
            submitButton.querySelector('.spinner-border').classList.remove('d-none');
            submitButton.querySelector('.button-text').textContent = 'Importando...';
        });
    }

    // SweetAlert para sucesso
    @if (session('sucesso'))
        Swal.fire({
            title: 'Sucesso!',
            text: '{{ session('sucesso') }}',
            icon: 'success',
            confirmButtonColor: '#0d6efd'
        });
    @endif
});
</script>
@endpush
```