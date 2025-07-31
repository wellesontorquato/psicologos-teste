<section class="form-section">
    <h3 class="text-lg font-semibold mb-3 text-gray-700">Informações do Perfil</h3>
    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div class="form-group">
            <input type="text" name="name" 
                   value="{{ old('name', auth()->user()->name) }}" 
                   class="input-style" 
                   placeholder="Nome">
        </div>

        <div class="form-group">
            <input type="email" name="email" 
                   value="{{ old('email', auth()->user()->email) }}" 
                   class="input-style" 
                   placeholder="Email">
        </div>

        <div class="form-group">
            <select id="genero" name="genero" class="input-style">
                <option value="" disabled {{ old('genero', auth()->user()->genero) === null ? 'selected' : '' }}>Gênero</option>
                <option value="masculino" {{ old('genero', auth()->user()->genero) === 'masculino' ? 'selected' : '' }}>Masculino</option>
                <option value="feminino" {{ old('genero', auth()->user()->genero) === 'feminino' ? 'selected' : '' }}>Feminino</option>
                <option value="outro" {{ old('genero', auth()->user()->genero) === 'outro' ? 'selected' : '' }}>Outro</option>
                <option value="prefiro não dizer" {{ old('genero', auth()->user()->genero) === 'prefiro não dizer' ? 'selected' : '' }}>Prefiro não dizer</option>
            </select>
        </div>

        <div class="form-group">
            <label for="link_principal" class="block text-sm font-medium text-gray-600 mb-1">
                Link principal (WhatsApp, Instagram ou outro)
            </label>
            <input type="url" id="link_principal" name="link_principal"
                   value="{{ old('link_principal', auth()->user()->link_principal) }}"
                   class="input-style"
                   placeholder="https://wa.me/5581999999999 ou https://instagram.com/psicologo">

            <small class="text-muted block mt-1">
                💡 Dica: você pode digitar apenas o número do WhatsApp (ex: <code>82999998888</code>) 
                que transformaremos automaticamente em um link válido. 
                Também pode colar links de Instagram, LinkedIn ou outras redes.
            </small>
        </div>

        <button type="submit" class="btn-primary">
            <i class="bi bi-save"></i> Salvar
        </button>
    </form>
</section>
