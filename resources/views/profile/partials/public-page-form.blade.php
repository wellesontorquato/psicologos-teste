<h3 class="card-title">Minha Página Pública</h3>

<form method="post" action="{{ route('profile.update.slug') }}" class="mt-4">
    @csrf
    @method('patch')
    
    <div class="form-group">
        <label for="slug">Personalize o final do seu link (ex: dr-joao-silva)</label>
        <div class="input-group">
            <input type="text" id="slug" name="slug"
                   value="{{ old('slug', auth()->user()->slug) }}"
                   class="input-style"
                   style="border-top-left-radius: 0; border-bottom-left-radius: 0;"
                   placeholder="seu-link-personalizado">
        </div>
        <small id="slugPreview" class="form-text text-muted d-block mt-2">
            Esse será o link que você poderá compartilhar com seus pacientes:<br>
            <span class="input-group-text" style="color:#00aaff">{{ url('/') }}/{{ old('slug', auth()->user()->slug ?? 'seu-link-ainda-nao-configurado') }}</span>
        </small>
    </div>

    <button type="submit" class="btn-primary">Salvar</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slugInput = document.getElementById('slug');
        const slugPreview = document.getElementById('slugPreview').querySelector('span');
        const baseUrl = "{{ url('/') }}/";

        function updatePreview() {
            const slugValue = slugInput.value.trim();
            if (slugValue.length > 0) {
                slugPreview.textContent = baseUrl + slugValue;
            } else {
                slugPreview.textContent = baseUrl + "seu-link-ainda-nao-configurado";
            }
        }

        // Atualiza na digitação
        slugInput.addEventListener('input', updatePreview);

        // Atualiza já no carregamento
        updatePreview();
    });
</script>
