<h3 class="card-title">Informações do Perfil</h3>

<style>
    .profile-help-tooltip {
        width: 22px;
        height: 22px;
        border: 0;
        border-radius: 999px;
        background: #e0ecff;
        color: #1d4ed8;
        font-size: 13px;
        font-weight: 800;
        line-height: 22px;
        text-align: center;
        cursor: pointer;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .2s ease;
    }

    .profile-help-tooltip:hover {
        background: #bfdbfe;
        color: #1e40af;
    }

    .profile-label-with-help {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }

    .profile-label-with-help label {
        margin-bottom: 0;
    }

    .profile-field-help {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: .85rem;
        line-height: 1.35;
    }
</style>

@php
    $user = auth()->user();

    $dataNascimento = old('data_nascimento');

    if ($dataNascimento === null && $user->data_nascimento) {
        try {
            $dataNascimento = \Illuminate\Support\Carbon::parse($user->data_nascimento)->format('Y-m-d');
        } catch (\Throwable $e) {
            $dataNascimento = $user->data_nascimento;
        }
    }

    $cpfNumeros = $user->cpf ? preg_replace('/\D/', '', (string) $user->cpf) : null;

    if ($cpfNumeros && strlen($cpfNumeros) === 11) {
        $cpfVisual = substr($cpfNumeros, 0, 3) . '.' .
                     substr($cpfNumeros, 3, 3) . '.' .
                     substr($cpfNumeros, 6, 3) . '-' .
                     substr($cpfNumeros, 9, 2);
    } else {
        $cpfVisual = $user->cpf;
    }

    $tipoProfissionalAtual = old('tipo_profissional', $user->tipo_profissional);
@endphp

<form method="post" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf
    @method('patch')

    <div class="form-group">
        <label for="name">Nome</label>
        <input type="text"
               id="name"
               name="name"
               value="{{ old('name', $user->name) }}"
               class="input-style @error('name') is-invalid @enderror"
               placeholder="Seu nome completo">

        @error('name')
            <small class="invalid-feedback">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email"
               id="email"
               name="email"
               value="{{ old('email', $user->email) }}"
               class="input-style @error('email') is-invalid @enderror"
               placeholder="seu@email.com">

        @error('email')
            <small class="invalid-feedback">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="genero">Gênero</label>
        <select id="genero"
                name="genero"
                class="input-style @error('genero') is-invalid @enderror">
            <option value="" disabled {{ old('genero', $user->genero) === null ? 'selected' : '' }}>
                Selecione seu gênero
            </option>

            <option value="masculino" {{ old('genero', $user->genero) === 'masculino' ? 'selected' : '' }}>
                Masculino
            </option>

            <option value="feminino" {{ old('genero', $user->genero) === 'feminino' ? 'selected' : '' }}>
                Feminino
            </option>

            <option value="outro" {{ old('genero', $user->genero) === 'outro' ? 'selected' : '' }}>
                Outro
            </option>

            <option value="prefiro não dizer" {{ old('genero', $user->genero) === 'prefiro não dizer' ? 'selected' : '' }}>
                Prefiro não dizer
            </option>
        </select>

        @error('genero')
            <small class="invalid-feedback">{{ $message }}</small>
        @enderror
    </div>

    <hr style="margin: 24px 0; border: 0; border-top: 1px solid #e5e7eb;">

    <h4 style="font-size: 1rem; font-weight: 700; color: #111827; margin-bottom: 12px;">
        Dados profissionais
    </h4>

    <div class="form-group">
        <label for="cpf_visual">CPF</label>
        <input type="text"
               id="cpf_visual"
               value="{{ $cpfVisual }}"
               class="input-style"
               placeholder="CPF não cadastrado"
               readonly
               disabled>

        <small class="profile-field-help">
            O CPF não pode ser alterado pelo perfil.
        </small>
    </div>

    <div class="form-group">
        <label for="data_nascimento">Data de nascimento</label>
        <input type="date"
               id="data_nascimento"
               name="data_nascimento"
               value="{{ $dataNascimento }}"
               class="input-style @error('data_nascimento') is-invalid @enderror">

        @error('data_nascimento')
            <small class="invalid-feedback">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="tipo_profissional">Tipo profissional</label>
        <select id="tipo_profissional"
                name="tipo_profissional"
                class="input-style @error('tipo_profissional') is-invalid @enderror"
                required>
            <option value="" disabled {{ $tipoProfissionalAtual ? '' : 'selected' }}>
                Selecione o tipo profissional
            </option>

            <option value="psicologo" {{ $tipoProfissionalAtual === 'psicologo' ? 'selected' : '' }}>
                Psicólogo(a)
            </option>

            <option value="psiquiatra" {{ $tipoProfissionalAtual === 'psiquiatra' ? 'selected' : '' }}>
                Psiquiatra
            </option>

            <option value="psicanalista" {{ $tipoProfissionalAtual === 'psicanalista' ? 'selected' : '' }}>
                Psicanalista
            </option>
        </select>

        @error('tipo_profissional')
            <small class="invalid-feedback">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group" id="registro-profissional-wrapper">
        <div class="profile-label-with-help">
            <label for="registro_profissional" id="registro-profissional-label">
                Registro profissional
            </label>

            <button type="button"
                    class="profile-help-tooltip"
                    aria-label="Ajuda sobre registro profissional"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    data-bs-container="body"
                    data-bs-html="true"
                    title="Informe o registro profissional com números, letras e traços, se houver, conforme consta no conselho. Exemplo para CRM: 12345-SP. Para CRP, preencha conforme consta no conselho ou no Carnê-Leão.">
                ?
            </button>
        </div>

        <input type="text"
               id="registro_profissional"
               name="registro_profissional"
               value="{{ old('registro_profissional', $user->registro_profissional) }}"
               class="input-style @error('registro_profissional') is-invalid @enderror"
               placeholder="Ex: 15/7179 ou conforme consta no conselho"
               maxlength="30">

        <small id="registro-profissional-ajuda" class="profile-field-help">
            Informe o registro profissional exatamente como consta no conselho ou no cadastro do Carnê-Leão.
        </small>

        @error('registro_profissional')
            <small class="invalid-feedback">{{ $message }}</small>
        @enderror
    </div>

    <button type="submit" class="btn-primary">
        Salvar Alterações
    </button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoSelect = document.getElementById('tipo_profissional');
    const registroLabel = document.getElementById('registro-profissional-label');
    const registroInput = document.getElementById('registro_profissional');
    const registroAjuda = document.getElementById('registro-profissional-ajuda');

    function atualizarCampoRegistro() {
        if (!tipoSelect || !registroLabel || !registroInput || !registroAjuda) {
            return;
        }

        const tipo = tipoSelect.value;

        if (tipo === 'psicologo') {
            registroLabel.textContent = 'CRP';
            registroInput.placeholder = 'Ex: 15/7179 ou conforme consta no conselho';
            registroAjuda.textContent = 'Informe o CRP conforme consta no conselho ou no cadastro do Carnê-Leão.';
            registroInput.required = true;
        } else if (tipo === 'psiquiatra') {
            registroLabel.textContent = 'CRM';
            registroInput.placeholder = 'Ex: 12345-SP';
            registroAjuda.textContent = 'Informe o CRM com número, traço e sigla do estado, conforme consta no conselho.';
            registroInput.required = true;
        } else if (tipo === 'psicanalista') {
            registroLabel.textContent = 'Registro profissional';
            registroInput.placeholder = 'Informe o registro, se houver';
            registroAjuda.textContent = 'Psicanalista pode usar o perfil, mas o Receita Saúde será liberado apenas para categorias compatíveis.';
            registroInput.required = false;
        } else {
            registroLabel.textContent = 'Registro profissional';
            registroInput.placeholder = 'Informe conforme consta no conselho';
            registroAjuda.textContent = 'Informe o registro profissional exatamente como consta no conselho ou no cadastro do Carnê-Leão.';
            registroInput.required = false;
        }
    }

    if (tipoSelect) {
        tipoSelect.addEventListener('change', atualizarCampoRegistro);
        atualizarCampoRegistro();
    }

    if (window.bootstrap && typeof bootstrap.Tooltip === 'function') {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');

        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>