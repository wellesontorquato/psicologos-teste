@extends('layouts.app')

@section('content')
<div class="container py-6">
    <h2 class="text-2xl font-bold mb-4">🗂️ Todas as Notícias</h2>
    <a href="{{ route('admin.news.create') }}" class="btn btn-success mb-4">
        <i class="bi bi-plus-lg"></i> Nova Notícia
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Título</th>
                <th>Imagem</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($news as $article)
                <tr>
                    <td>{{ $article->title }}</td>
                    <td>
                        @if ($article->image)
                            <img src="{{ Storage::disk('s3')->url($article->image) }}" class="w-16 rounded">
                        @endif
                    </td>
                    <td>{{ $article->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-sm btn-warning">Editar</a>

                        <form action="{{ route('admin.news.destroy', $article) }}" method="POST" class="d-inline delete-form no-spinner">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Deletar com confirmação SweetAlert2
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Tem certeza?',
                text: "Essa ação não poderá ser desfeita.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ✅ Fecha o modal SweetAlert2 suavemente
                    Swal.close();

                    // ✅ Dá um leve delay para garantir que o SweetAlert fechou antes de mostrar o spinner
                    setTimeout(() => {
                        if (typeof showSpinner === 'function') {
                            showSpinner();
                        }
                        form.submit();
                    }, 300); // 300ms deixa a animação do SweetAlert fechar bonito
                }
            });
        });
    });

    // Mensagens de sucesso ou erro
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '{{ session('success') }}',
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: '{{ session('error') }}',
            confirmButtonText: 'OK'
        });
    @endif
</script>
@endpush
