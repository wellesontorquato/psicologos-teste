<div class="form-section">
    <h3 class="section-title">Minha Página Pública</h3>

    <p>
        Seu link: 
        <a href="{{ url(auth()->user()->slug ?? '') }}" target="_blank">
            {{ url(auth()->user()->slug ?? '') }}
        </a>
    </p>

    <form method="POST" action="{{ route('profile.update.slug') }}">
        @csrf
        @method('PATCH')
        
        <div class="form-group">
            <label for="slug">Slug da sua página (ex: dr-joao-silva)</label>
            <input type="text" id="slug" name="slug"
                   value="{{ old('slug', auth()->user()->slug) }}"
                   class="input-style">
        </div>

        <button type="submit" class="form-button">Salvar</button>
    </form>
</div>
