@extends('layouts.app')

@section('title', 'Arquivos | PsiGestor')

@section('content')
<div class="container">
    <h2 class="mb-4">Arquivos de {{ $paciente->nome }}</h2>

    <!-- Botão para abrir modal -->
    <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
        Enviar Novo Arquivo
    </button>

    <!-- Modal de upload -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('arquivos.store', $paciente->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Enviar Arquivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="file" name="arquivo" class="form-control" required>
                    @error('arquivo')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de arquivos -->
    @if ($paciente->arquivos->isEmpty())
        <div class="alert alert-info">Nenhum arquivo enviado ainda.</div>
    @else
        <div class="list-group">
            @foreach ($paciente->arquivos as $arquivo)
                <div class="list-group-item d-flex justify-content-between align-items-start flex-wrap">
                    <div class="me-auto">
                        <a href="{{ $arquivo->url }}" target="_blank" class="fw-bold">
                            {{ $arquivo->nome }}
                        </a>
                        <div class="text-muted small">Enviado em: {{ $arquivo->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ $arquivo->url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            Visualizar
                        </a>

                        <!-- Botão de Renomear -->
                        <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editarArquivoModal{{ $arquivo->id }}">
                            Renomear
                        </button>

                        <!-- Modal Renomear -->
                        <div class="modal fade" id="editarArquivoModal{{ $arquivo->id }}" tabindex="-1" aria-labelledby="editarArquivoModalLabel{{ $arquivo->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('arquivos.rename', $arquivo) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editarArquivoModalLabel{{ $arquivo->id }}">Renomear Arquivo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="text" name="nome" class="form-control" value="{{ $arquivo->nome }}" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Salvar</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Botão Excluir -->
                        <form method="POST" action="{{ route('arquivos.destroy', $arquivo) }}" class="d-inline delete-form no-spinner">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <a href="{{ route('pacientes.index') }}" class="btn btn-secondary mt-4">Voltar</a>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Tem certeza?',
                text: "Este arquivo será excluído permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ✅ Primeiro fecha o SweetAlert
                    Swal.close();

                    // ✅ Aguarda fechar suavemente, depois ativa spinner + submit
                    setTimeout(() => {
                        if (typeof showSpinner === 'function') {
                            showSpinner();
                        }
                        form.submit();
                    }, 300); // Delay de ~300ms para a animação fechar antes de iniciar o spinner
                }
            });
        });
    });
</script>
@endsection


