<h3 class="card-title">Minha Página Pública</h3>

<div class="public-link-display">
    <span class="font-weight-bold">Seu link:</span>
    <a href="{{ url(auth()->user()->slug ?? '') }}" target="_blank">
        {{-- Usamos a helper 'url' para mostrar o link completo e amigável --}}
        {{ url(auth()->user()->slug ?? 'seu-link-ainda-nao-configurado') }}
    </a>
</div>

<form method="post" action="{{ route('profile.update') }}" class="mt-4">
    @csrf
    @method('patch')
    
    <div class="form-group">
        <label for="slug">Personalize o final do seu link (ex: dr-joao-silva)</label>
        <div class="input-group">
            <span class="input-group-text">{{ url('/') }}/</span>
            <input type="text" id="slug" name="slug"
                   value="{{ old('slug', auth()->user()->slug) }}"
                   class="input-style"
                   style="border-top-left-radius: 0; border-bottom-left-radius: 0;"
                   placeholder="seu-link-personalizado">
        </div>
    </div>

    {{-- A classe 'btn-primary' garante o mesmo estilo dos outros botões --}}
    <button type="submit" class="btn-primary">Salvar</button>
</form>