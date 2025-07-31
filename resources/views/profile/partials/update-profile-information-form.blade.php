<h3 class="card-title">Informações do Perfil</h3>

<form method="post" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf
    @method('patch')

    <div class="form-group">
        <label for="name">Nome</label>
        <input type="text" id="name" name="name" 
               value="{{ old('name', auth()->user()->name) }}" 
               class="input-style" 
               placeholder="Seu nome completo">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" 
               value="{{ old('email', auth()->user()->email) }}" 
               class="input-style" 
               placeholder="seu@email.com">
    </div>

    <div class="form-group">
        <label for="genero">Gênero</label>
        <select id="genero" name="genero" class="input-style">
            <option value="" disabled {{ old('genero', auth()->user()->genero) === null ? 'selected' : '' }}>Selecione seu gênero</option>
            <option value="masculino" {{ old('genero', auth()->user()->genero) === 'masculino' ? 'selected' : '' }}>Masculino</option>
            <option value="feminino" {{ old('genero', auth()->user()->genero) === 'feminino' ? 'selected' : '' }}>Feminino</option>
            <option value="outro" {{ old('genero', auth()->user()->genero) === 'outro' ? 'selected' : '' }}>Outro</option>
            <option value="prefiro não dizer" {{ old('genero', auth()->user()->genero) === 'prefiro não dizer' ? 'selected' : '' }}>Prefiro não dizer</option>
        </select>
    </div>

    <div class="form-group">
        <label for="link_principal">
            Link principal (WhatsApp, Instagram, etc.)
        </label>
        <input type="url" id="link_principal" name="link_principal"
               value="{{ old('link_principal', auth()->user()->link_principal) }}"
               class="input-style"
               placeholder="https://wa.me/5582999998888">

        <small class="text-muted block mt-1" style="color: #6c757d; font-size: 0.875em;">
            💡 Dica: digite apenas o número do WhatsApp (ex: <code>82999998888</code>) ou cole o link da sua rede social.
        </small>
    </div>

    <button type="submit" class="btn-primary">
        Salvar Alterações
    </button>
</form>