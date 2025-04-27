<details class="form-section">
    <summary class="cursor-pointer text-sm font-semibold mb-2 text-gray-700">
        <i class="bi bi-lock-fill text-yellow-500"></i> Atualizar Senha
    </summary>

    <form method="POST" action="{{ route('profile.password') }}" class="space-y-4 mt-4">
        @csrf
        @method('PUT')

        <div class="form-group">
            <input type="password" name="current_password" placeholder="Senha atual" autocomplete="current-password"
                class="input-style @error('current_password') border-red-500 @enderror">
            @error('current_password')
                <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <input type="password" name="password" placeholder="Nova senha" autocomplete="new-password"
                class="input-style @error('password') border-red-500 @enderror">
            @error('password')
                <small class="text-red-600">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <input type="password" name="password_confirmation" placeholder="Confirmar nova senha" autocomplete="new-password"
                class="input-style">
        </div>

        <button type="submit" class="btn-primary">
            <i class="bi bi-shield-lock-fill"></i> Atualizar Senha
        </button>
    </form>
</details>
