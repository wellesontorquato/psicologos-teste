<details class="form-section">
    <summary class="cursor-pointer text-sm font-semibold mb-2 text-red-700">
        <i class="bi bi-exclamation-triangle-fill text-red-600"></i> Excluir Conta
    </summary>

    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4 mt-4" id="form-delete-user">
        @csrf
        @method('delete')

        <p class="text-sm text-gray-600">
            Uma vez excluída, sua conta e dados serão permanentemente apagados. Esta ação não poderá ser desfeita.
        </p>

        <div class="form-group">
            <input type="password" name="password" placeholder="Confirme sua senha"
                class="input-style @error('password') border-red-500 @enderror">
            @error('password') <small class="text-red-600">{{ $message }}</small> @enderror
        </div>

        <button type="button" class="btn-danger" onclick="confirmDelete()">
            <i class="bi bi-trash-fill"></i> Excluir Conta
        </button>
    </form>

    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Você tem certeza?',
                text: "Esta ação é irreversível!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#00aaff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-user').submit();
                }
            });
        }
    </script>
</details>
