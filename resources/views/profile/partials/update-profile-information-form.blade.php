<h3 class="card-title">Informações do Perfil</h3>

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
        <label for="cpf">CPF</label>
        <input type="text"
               id="cpf"
               name="cpf"
               value="{{ old('cpf', $user->cpf) }}"
               class="input-style @error('cpf') is-invalid @enderror"
               placeholder="000.000.000-00"
               maxlength="14"
               inputmode="numeric">

        @error('cpf')
            <small class="invalid-feedback">{{ $message }}</small>
        @enderror
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
                class="input-style @error('tipo_profissional') is-invalid @enderror">
            <option value="" {{ old('tipo_profissional', $user->tipo_profissional) ? '' : 'selected' }}>
                Selecione o tipo profissional
            </option>

            <option value="psicologo" {{ old('tipo_profissional', $user->tipo_profissional) === 'psicologo' ? 'selected' : '' }}>
                Psicólogo(a)
            </option>

            <option value="psiquiatra" {{ old('tipo_profissional', $user->tipo_profissional) === 'psiquiatra' ? 'selected' : '' }}>
                Psiquiatra
            </option>

            <option value="psicanalista" {{ old('tipo_profissional', $user->tipo_profissional) === 'psicanalista' ? 'selected' : '' }}>
                Psicanalista
            </option>
        </select>

        @error('tipo_profissional')
            <small class="invalid-feedback">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group" id="registro-profissional-wrapper">
        <label for="registro_profissional" id="registro-profissional-label">
            Registro profissional
        </label>

        <input type="text"
               id="registro_profissional"
               name="registro_profissional"
               value="{{ old('registro_profissional', $user->registro_profissional) }}"
               class="input-style @error('registro_profissional') is-invalid @enderror"
               placeholder="Ex: CRP 06/000000"
               maxlength="30">

        <small id="registro-profissional-ajuda" style="display:block; margin-top:6px; color:#64748b;">
            Para psicólogo, informe o CRP. Para psiquiatra, informe o CRM.
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
    const cpfInput = document.getElementById('cpf');
    const tipoSelect = document.getElementById('tipo_profissional');
    const registroLabel = document.getElementById('registro-profissional-label');
    const registroInput = document.getElementById('registro_profissional');
    const registroAjuda = document.getElementById('registro-profissional-ajuda');

    function aplicarMascaraCpf(valor) {
        valor = valor.replace(/\D/g, '').slice(0, 11);

        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

        return valor;
    }

    function atualizarCampoRegistro() {
        const tipo = tipoSelect.value;

        if (tipo === 'psicologo') {
            registroLabel.textContent = 'CRP';
            registroInput.placeholder = 'Ex: CRP 06/000000';
            registroAjuda.textContent = 'Informe o número do CRP do profissional.';
            registroInput.disabled = false;
        } else if (tipo === 'psiquiatra') {
            registroLabel.textContent = 'CRM';
            registroInput.placeholder = 'Ex: CRM/SP 000000';
            registroAjuda.textContent = 'Informe o número do CRM do profissional.';
            registroInput.disabled = false;
        } else if (tipo === 'psicanalista') {
            registroLabel.textContent = 'Registro profissional';
            registroInput.placeholder = 'Informe o registro, se houver';
            registroAjuda.textContent = 'Psicanalista pode usar o perfil, mas o Receita Saúde será liberado apenas para categorias compatíveis.';
            registroInput.disabled = false;
        } else {
            registroLabel.textContent = 'Registro profissional';
            registroInput.placeholder = 'Ex: CRP 06/000000';
            registroAjuda.textContent = 'Para psicólogo, informe o CRP. Para psiquiatra, informe o CRM.';
            registroInput.disabled = false;
        }
    }

    if (cpfInput) {
        cpfInput.value = aplicarMascaraCpf(cpfInput.value);

        cpfInput.addEventListener('input', function () {
            this.value = aplicarMascaraCpf(this.value);
        });
    }

    if (tipoSelect) {
        tipoSelect.addEventListener('change', atualizarCampoRegistro);
        atualizarCampoRegistro();
    }
});
</script>