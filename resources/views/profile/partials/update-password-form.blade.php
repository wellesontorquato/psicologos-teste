{{-- A tag <details> é mantida, mas sem a classe .form-section --}}
<details>
    <summary class="card-summary">
        Atualizar Senha
    </summary>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4 mt-4">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="current_password">Senha atual</label>
            <input type="password" id="current_password" name="current_password" 
                   autocomplete="current-password"
                   class="input-style @error('current_password', 'updatePassword') is-invalid @enderror">
            
            @error('current_password', 'updatePassword')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Nova senha</label>
            <input type="password" id="password" name="password" 
                   autocomplete="new-password"
                   class="input-style @error('password', 'updatePassword') is-invalid @enderror">
            
            @error('password', 'updatePassword')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmar nova senha</label>
            <input type="password" id="password_confirmation" name="password_confirmation" 
                   autocomplete="new-password"
                   class="input-style">
        </div>

        <button type="submit" class="btn-primary">
            Atualizar Senha
        </button>
    </form>
</details>