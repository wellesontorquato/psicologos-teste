<div class="profile-card">
    <h3 class="section-title">🌐 Landing Page Pública</h3>
    <p class="text-muted mb-4">
        Configure as informações exibidas na sua página pública.
    </p>

    <form method="POST" action="{{ route('profile.update.landing') }}">
        @csrf
        @method('PATCH')

        {{-- SLUG COM PREVIEW DINÂMICO --}}
        <div class="form-group mb-3">
            <label for="slug">Endereço da sua página</label>
            <div class="input-group">
                <input type="text" id="slug" name="slug"
                       class="input-style"
                       value="{{ old('slug', $user->slug) }}"
                       placeholder="Ex.: seu-nome">
            </div>
            <small id="slugPreview" class="form-text text-muted d-block mt-2">
                Esse será o link que você poderá compartilhar com seus pacientes:<br>
                <span class="public-link">
                    {{ url('/') }}/{{ old('slug', $user->slug ?? 'seu-link-ainda-nao-configurado') }}
                </span>
            </small>
        </div>

        {{-- BIO --}}
        <div class="form-group mb-3">
            <label for="bio">Descrição (Sobre Você)</label>
            <textarea id="bio" name="bio" rows="4" class="input-style"
                      placeholder="Conte um pouco sobre você...">{{ old('bio', $user->bio) }}</textarea>
        </div>

        {{-- WHATSAPP --}}
        <div class="form-group mb-3">
            <label for="whatsapp">WhatsApp</label>
            <input type="text" id="whatsapp" name="whatsapp"
                   class="input-style"
                   value="{{ old('whatsapp', $user->whatsapp) }}"
                   placeholder="DDD + Número (ex: 11999998888)">
        </div>

        {{-- LINKS EXTRAS --}}
        <div class="form-group">
            <label for="link_principal">
                Link principal (WhatsApp, Instagram, etc.)
            </label>
            <input type="text" id="link_principal" name="link_principal"
                value="{{ old('link_principal', auth()->user()->link_principal) }}"
                class="input-style"
                placeholder="https://wa.me/5582999998888">

            <small class="text-muted block mt-1" style="color: #6c757d; font-size: 0.875em;">
                💡 Dica: digite apenas o número do WhatsApp (ex: <code>82999998888</code>) ou cole o link da sua rede social.
            </small>
        </div>

        <div class="form-group">
            <label for="link_extra1">Link adicional 1</label>
            <input type="url" id="link_extra1" name="link_extra1"
                value="{{ old('link_extra1', auth()->user()->link_extra1) }}"
                class="input-style"
                placeholder="https://instagram.com/psicologo">
        </div>

        <div class="form-group">
            <label for="link_extra2">Link adicional 2</label>
            <input type="url" id="link_extra2" name="link_extra2"
                value="{{ old('link_extra2', auth()->user()->link_extra2) }}"
                class="input-style"
                placeholder="https://linkedin.com/in/psicologo">
        </div>


        {{-- ÁREAS DE ATUAÇÃO --}}
        <div class="form-group mb-4">
            <label>Áreas de Atuação</label>
            @php
                $areasSelecionadas = old('areas', json_decode($user->areas ?? '[]', true));
                $todasAreas = [
                    // Psicologia Clínica
                    'Psicoterapia Individual',
                    'Psicoterapia Infantil',
                    'Psicoterapia Adolescente',
                    'Psicoterapia de Adultos',
                    'Psicoterapia de Idosos',
                    'Terapia de Casal',
                    'Terapia Familiar',
                    'Psicologia Clínica',
                    'Psicanálise',
                    
                    // Avaliação e Diagnóstico
                    'Avaliação Psicológica',
                    'Avaliação Neuropsicológica',
                    'Avaliação para Cirurgia Bariátrica',
                    'Avaliação para CNH',
                    'Avaliação para Concursos e Seleções',

                    // Psiquiatria
                    'Psiquiatria Geral',
                    'Psiquiatria Infantil',
                    'Psiquiatria da Adolescência',
                    'Psiquiatria Geriátrica',
                    'Saúde Mental',
                    'Transtornos de Humor',
                    'Transtornos de Ansiedade',
                    'Transtornos Alimentares',
                    'Dependência Química',

                    // Áreas de Especialidade
                    'Neuropsicologia',
                    'Psicologia Hospitalar',
                    'Psicologia Organizacional',
                    'Psicologia Escolar',
                    'Psicologia do Esporte',
                    'Psicologia Jurídica',
                    'Psicologia Social',
                    'Terapia Ocupacional',
                    
                    // Temáticas específicas
                    'Acompanhamento Psicológico Escolar',
                    'Orientação Profissional e de Carreira',
                    'Terapia em Grupo',
                    'Sexualidade e Terapia Sexual',
                    'Violência Doméstica e Abuso',
                    'Luto e Perdas',
                    'Estresse e Burnout',
                    'Transtorno do Espectro Autista',
                    'Transtorno de Déficit de Atenção e Hiperatividade (TDAH)',
                    'Transtornos do Sono',
                    'Tratamento de Ansiedade',
                    'Tratamento de Depressão',
                    'Terapia para Fobias',
                ];
            @endphp

            <div class="areas-list">
                @foreach($todasAreas as $area)
                    <label class="area-item">
                        <input type="checkbox" name="areas[]" value="{{ $area }}"
                               {{ in_array($area, $areasSelecionadas ?? []) ? 'checked' : '' }}>
                        {{ $area }}
                    </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn-primary">Salvar Alterações</button>
    </form>
</div>

<style>
    .areas-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .area-item {
        background: #f1f5f9;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        cursor: pointer;
        user-select: none;
        transition: background 0.2s ease-in-out;
    }
    .area-item:hover {
        background: #e2e8f0;
    }
    .area-item input {
        margin-right: 5px;
    }
    .public-link {
    display: inline-block;
    max-width: 100%;
    color: #00aaff;
    font-weight: 500;
    word-break: break-word;
    overflow-wrap: anywhere;
    white-space: normal;
    line-height: 1.4;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slugInput = document.getElementById('slug');
        const slugPreview = document.querySelector('#slugPreview span');
        const baseUrl = "{{ url('/') }}/";

        function updatePreview() {
            const slugValue = slugInput.value.trim();
            if (slugValue.length > 0) {
                slugPreview.textContent = baseUrl + slugValue;
            } else {
                slugPreview.textContent = baseUrl + "seu-link-ainda-nao-configurado";
            }
        }

        slugInput.addEventListener('input', updatePreview);
        updatePreview();
    });
</script>
