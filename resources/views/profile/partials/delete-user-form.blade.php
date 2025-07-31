{{-- A tag <details> é mantida, mas sem a classe .form-section --}}
<details>
    {{-- Usamos a classe .card-summary e adicionamos um modificador .text-danger para a cor --}}
    <summary class="card-summary text-danger">
        Excluir Conta
    </summary>

    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4 mt-4" id="form-delete-user">
        @csrf
        @method('delete')

        <p class="form-text-warning">
            Uma vez que sua conta for excluída, todos os seus recursos e dados serão permanentemente apagados. Por favor, digite sua senha para confirmar que você deseja excluir permanentemente sua conta.
        </p>

        <div class="form-group">
            <label for="password-delete">Confirme sua senha</label>
            <input type="password" id="password-delete" name="password"
                   class="input-style @error('password', 'userDeletion') is-invalid @enderror">
            
            {{-- Verificando o erro na 'error bag' correta: 'userDeletion' --}}
            @error('password', 'userDeletion')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        {{-- O botão já usa a classe .btn-danger que definimos no início --}}
        <button type="button" class="btn-danger" onclick="confirmDelete()">
            Excluir Conta Permanentemente
        </button>
    </form>
</details>

{{-- O script pode permanecer aqui, pois é específico para este componente --}}
<script>
    function confirmDelete() {
        Swal.fire({
            title: 'Você tem certeza absoluta?',
            text: "Esta ação é IRREVERSÍVEL. Todos os seus dados serão apagados para sempre.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545', // Vermelho para o botão de confirmação
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir minha conta',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Adiciona um pequeno delay para o usuário ver a confirmação
                Swal.fire('Excluindo...', 'Sua conta está sendo removida.', 'info');
                document.getElementById('form-delete-user').submit();
            }
        });
    }
</script>