@extends('layouts.app')

@section('title', 'Criar Paciente | PsiGestor')

@section('content')
<style>
    .pg-page {
        width: 100%;
    }

    .pg-header {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 22px;
    }

    .pg-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .pg-subtitle {
        color: #64748b;
        margin: 4px 0 0;
        font-size: .95rem;
    }

    .pg-card {
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 22px;
        box-shadow: 0 14px 38px rgba(15,23,42,.08);
        padding: 18px;
        margin-bottom: 18px;
    }

    .pg-section-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .pg-section-kicker {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #2563eb;
        background: #eff6ff;
        border-radius: 999px;
        padding: 6px 10px;
        margin-bottom: 8px;
    }

    .pg-section-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .pg-section-description {
        color: #64748b;
        font-size: .9rem;
        margin: 4px 0 0;
    }

    .pg-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .pg-grid-2,
    .pg-grid-3,
    .pg-grid-4 {
        grid-template-columns: 1fr;
    }

    .pg-field label {
        font-size: .86rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }

    .pg-field .form-control,
    .pg-field .form-select {
        min-height: 44px;
        border-radius: 14px;
        border-color: #dbe3ef;
        font-size: .95rem;
        color: #0f172a;
    }

    .pg-field .form-control:focus,
    .pg-field .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 .2rem rgba(37,99,235,.12);
    }

    .pg-help {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: .82rem;
        line-height: 1.35;
    }

    .pg-switch-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 14px;
    }

    .pg-switch-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .pg-switch-title {
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        font-size: .96rem;
    }

    .pg-switch-description {
        color: #64748b;
        font-size: .84rem;
        margin: 4px 0 0;
        line-height: 1.35;
    }

    .pg-help-tooltip {
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
        flex: 0 0 auto;
    }

    .pg-help-tooltip:hover {
        background: #bfdbfe;
        color: #1e40af;
    }

    .pg-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 20px;
    }

    .pg-btn {
        min-height: 44px;
        border-radius: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .pg-required-note {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        border-radius: 16px;
        padding: 12px 14px;
        font-size: .87rem;
        line-height: 1.45;
        margin-bottom: 18px;
    }

    @media (min-width: 768px) {
        .pg-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .pg-card {
            padding: 24px;
        }

        .pg-grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .pg-grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .pg-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .pg-actions {
            grid-template-columns: auto auto;
            justify-content: flex-end;
        }
    }

    @media (min-width: 1200px) {
        .pg-content {
            max-width: 1180px;
        }
    }
</style>

<div class="container-fluid py-2 pg-page">
    <div class="pg-content">
        <div class="pg-header">
            <div>
                <h1 class="pg-title">Novo paciente</h1>
                <p class="pg-subtitle">
                    Cadastre os dados pessoais, fiscais, contato de emergência e endereço do paciente.
                </p>
            </div>

            <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary pg-btn">
                <i class="bi bi-arrow-left"></i>
                Voltar para pacientes
            </a>
        </div>

        <div class="pg-required-note">
            <strong>Importante:</strong> os campos obrigatórios ajudam a manter a agenda, prontuário e dados fiscais organizados desde o primeiro atendimento.
        </div>

        <form id="form-paciente" action="{{ route('pacientes.store') }}" method="POST">
            @csrf

            <div class="pg-card">
                <div class="pg-section-header">
                    <div>
                        <div class="pg-section-kicker">
                            <i class="bi bi-person-plus"></i>
                            Identificação
                        </div>
                        <h2 class="pg-section-title">Dados principais</h2>
                        <p class="pg-section-description">
                            Informações básicas usadas na agenda, prontuário e controle financeiro.
                        </p>
                    </div>
                </div>

                <div class="pg-grid pg-grid-2">
                    <div class="pg-field">
                        <label for="nome">Nome completo</label>
                        <input type="text"
                               id="nome"
                               name="nome"
                               class="form-control"
                               value="{{ old('nome') }}"
                               placeholder="Nome completo do paciente"
                               required>
                    </div>

                    <div class="pg-field">
                        <label for="data_nascimento">Data de nascimento</label>
                        <input type="date"
                               id="data_nascimento"
                               name="data_nascimento"
                               class="form-control"
                               value="{{ old('data_nascimento') }}"
                               required>
                    </div>
                </div>

                <div class="pg-grid pg-grid-3 mt-3">
                    <div class="pg-field">
                        <label for="sexo">Sexo</label>
                        <select id="sexo"
                                name="sexo"
                                class="form-select"
                                required>
                            <option value="">Selecione</option>
                            <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>
                                Masculino
                            </option>
                            <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>
                                Feminino
                            </option>
                            <option value="Outro" {{ old('sexo') === 'Outro' ? 'selected' : '' }}>
                                Outro
                            </option>
                        </select>
                    </div>

                    <div class="pg-field">
                        <label for="telefone">Telefone</label>
                        <input type="text"
                               id="telefone"
                               name="telefone"
                               class="form-control js-phone-mask"
                               value="{{ old('telefone') }}"
                               placeholder="(00) 00000-0000"
                               inputmode="numeric"
                               required>
                    </div>

                    <div class="pg-field">
                        <label for="email">Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control"
                               value="{{ old('email') }}"
                               placeholder="paciente@email.com"
                               required>
                    </div>
                </div>
            </div>

            <div class="pg-card">
                <div class="pg-section-header">
                    <div>
                        <div class="pg-section-kicker">
                            <i class="bi bi-receipt"></i>
                            Receita Saúde
                        </div>
                        <h2 class="pg-section-title">Dados fiscais do paciente</h2>
                        <p class="pg-section-description">
                            Informações usadas para preparar recibos e arquivos de integração.
                        </p>
                    </div>
                </div>

                <div class="pg-grid pg-grid-2">
                    <div class="pg-field">
                        <label for="cpf">CPF</label>
                        <input type="text"
                               id="cpf"
                               name="cpf"
                               class="form-control js-cpf-mask"
                               value="{{ old('cpf') }}"
                               placeholder="000.000.000-00"
                               maxlength="14"
                               inputmode="numeric"
                               required>

                        <small class="pg-help">
                            Esse CPF poderá ser usado como beneficiário/pagador nos rascunhos do Receita Saúde.
                        </small>
                    </div>

                    <div class="pg-switch-card">
                        <input type="hidden" name="exige_nota_fiscal" value="0">

                        <div class="pg-switch-row">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <p class="pg-switch-title">Receita Saúde</p>

                                    <button type="button"
                                            class="pg-help-tooltip"
                                            aria-label="Ajuda sobre Receita Saúde"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-bs-container="body"
                                            data-bs-html="true"
                                            title="O PsiGestor prepara o arquivo de integração. A emissão oficial ainda deve ser concluída pelo profissional no Carnê-Leão Web/e-CAC.">
                                        ?
                                    </button>
                                </div>

                                <p class="pg-switch-description">
                                    Marque se este paciente costuma precisar de recibo/registro para Receita Saúde.
                                </p>
                            </div>

                            <div class="form-check form-switch mt-1">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="exige_nota_fiscal"
                                       value="1"
                                       id="exigeNotaFiscal"
                                       {{ old('exige_nota_fiscal') ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pg-card">
                <div class="pg-section-header">
                    <div>
                        <div class="pg-section-kicker">
                            <i class="bi bi-telephone"></i>
                            Emergência
                        </div>
                        <h2 class="pg-section-title">Contato de emergência</h2>
                        <p class="pg-section-description">
                            Pessoa de referência para situações importantes.
                        </p>
                    </div>
                </div>

                <div class="pg-grid pg-grid-3">
                    <div class="pg-field">
                        <label for="nome_contato_emergencia">Nome do contato</label>
                        <input type="text"
                               id="nome_contato_emergencia"
                               name="nome_contato_emergencia"
                               class="form-control"
                               placeholder="Nome completo"
                               value="{{ old('nome_contato_emergencia') }}">
                    </div>

                    <div class="pg-field">
                        <label for="telefone_contato_emergencia">Telefone do contato</label>
                        <input type="text"
                               id="telefone_contato_emergencia"
                               name="telefone_contato_emergencia"
                               class="form-control js-phone-mask"
                               placeholder="(00) 00000-0000"
                               inputmode="numeric"
                               value="{{ old('telefone_contato_emergencia') }}">
                    </div>

                    <div class="pg-field">
                        <label for="parentesco_contato_emergencia">Parentesco</label>
                        <select id="parentesco_contato_emergencia"
                                name="parentesco_contato_emergencia"
                                class="form-select">
                            <option value="">Selecione</option>
                            <option value="Pai" {{ old('parentesco_contato_emergencia') === 'Pai' ? 'selected' : '' }}>Pai</option>
                            <option value="Mãe" {{ old('parentesco_contato_emergencia') === 'Mãe' ? 'selected' : '' }}>Mãe</option>
                            <option value="Cônjuge" {{ old('parentesco_contato_emergencia') === 'Cônjuge' ? 'selected' : '' }}>Cônjuge</option>
                            <option value="Filho(a)" {{ old('parentesco_contato_emergencia') === 'Filho(a)' ? 'selected' : '' }}>Filho(a)</option>
                            <option value="Irmão(ã)" {{ old('parentesco_contato_emergencia') === 'Irmão(ã)' ? 'selected' : '' }}>Irmão(ã)</option>
                            <option value="Amigo(a)" {{ old('parentesco_contato_emergencia') === 'Amigo(a)' ? 'selected' : '' }}>Amigo(a)</option>
                            <option value="Outro" {{ old('parentesco_contato_emergencia') === 'Outro' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pg-card">
                <div class="pg-section-header">
                    <div>
                        <div class="pg-section-kicker">
                            <i class="bi bi-geo-alt"></i>
                            Endereço
                        </div>
                        <h2 class="pg-section-title">Localização</h2>
                        <p class="pg-section-description">
                            Preencha o CEP para buscar rua, bairro, cidade e UF automaticamente.
                        </p>
                    </div>
                </div>

                <div class="pg-grid pg-grid-4">
                    <div class="pg-field">
                        <label for="cep">CEP</label>
                        <input type="text"
                               id="cep"
                               name="cep"
                               class="form-control js-cep-mask"
                               value="{{ old('cep') }}"
                               placeholder="00000-000"
                               maxlength="9"
                               inputmode="numeric">
                    </div>

                    <div class="pg-field">
                        <label for="rua">Rua</label>
                        <input type="text"
                               id="rua"
                               name="rua"
                               class="form-control"
                               value="{{ old('rua') }}"
                               placeholder="Rua, avenida, travessa...">
                    </div>

                    <div class="pg-field">
                        <label for="numero">Número</label>
                        <input type="text"
                               id="numero"
                               name="numero"
                               class="form-control"
                               value="{{ old('numero') }}"
                               placeholder="Número">
                    </div>

                    <div class="pg-field">
                        <label>&nbsp;</label>
                        <div class="pg-switch-card py-2">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="sem_numero"
                                       id="sem_numero"
                                       {{ old('sem_numero') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="sem_numero">
                                    Sem número
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pg-grid pg-grid-4 mt-3">
                    <div class="pg-field">
                        <label for="complemento">Complemento</label>
                        <input type="text"
                               id="complemento"
                               name="complemento"
                               class="form-control"
                               value="{{ old('complemento') }}"
                               placeholder="Apto, bloco, referência...">
                    </div>

                    <div class="pg-field">
                        <label for="bairro">Bairro</label>
                        <input type="text"
                               id="bairro"
                               name="bairro"
                               class="form-control"
                               value="{{ old('bairro') }}">
                    </div>

                    <div class="pg-field">
                        <label for="cidade">Cidade</label>
                        <input type="text"
                               id="cidade"
                               name="cidade"
                               class="form-control"
                               value="{{ old('cidade') }}">
                    </div>

                    <div class="pg-field">
                        <label for="uf">UF</label>
                        <input type="text"
                               id="uf"
                               name="uf"
                               class="form-control"
                               value="{{ old('uf') }}"
                               maxlength="2"
                               placeholder="UF">
                    </div>
                </div>
            </div>

            <div class="pg-card">
                <div class="pg-section-header">
                    <div>
                        <div class="pg-section-kicker">
                            <i class="bi bi-clipboard2-pulse"></i>
                            Complementares
                        </div>
                        <h2 class="pg-section-title">Observações e medicação</h2>
                        <p class="pg-section-description">
                            Informações auxiliares para acompanhamento interno.
                        </p>
                    </div>
                </div>

                <div class="pg-grid">
                    <div class="pg-field">
                        <label for="observacoes">Observações</label>
                        <textarea id="observacoes"
                                  name="observacoes"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Anotações gerais sobre o paciente">{{ old('observacoes') }}</textarea>
                    </div>

                    <div class="pg-field">
                        <label for="medicacao_inicial">Medicação inicial</label>
                        <input type="text"
                               id="medicacao_inicial"
                               name="medicacao_inicial"
                               class="form-control"
                               placeholder="Ex: Sertralina 50mg"
                               value="{{ old('medicacao_inicial') }}">

                        <small class="pg-help">
                            Se informado, será registrada automaticamente no histórico.
                        </small>
                    </div>
                </div>
            </div>

            <div class="pg-actions">
                <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary pg-btn">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-success pg-btn">
                    <i class="bi bi-check2-circle"></i>
                    Salvar paciente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formPaciente = document.getElementById('form-paciente');
    const cepInput = document.getElementById('cep');
    const cpfInputs = document.querySelectorAll('.js-cpf-mask');
    const phoneInputs = document.querySelectorAll('.js-phone-mask');
    const cepInputs = document.querySelectorAll('.js-cep-mask');
    const semNumeroCheckbox = document.getElementById('sem_numero');
    const numeroInput = document.getElementById('numero');

    function apenasNumeros(valor) {
        return String(valor || '').replace(/\D/g, '');
    }

    function aplicarMascaraCpf(valor) {
        valor = apenasNumeros(valor).slice(0, 11);

        if (valor.length <= 3) {
            return valor;
        }

        if (valor.length <= 6) {
            return valor.replace(/(\d{3})(\d+)/, '$1.$2');
        }

        if (valor.length <= 9) {
            return valor.replace(/(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
        }

        return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
    }

    function aplicarMascaraTelefone(valor) {
        valor = apenasNumeros(valor).slice(0, 11);

        if (valor.length <= 2) {
            return valor;
        }

        if (valor.length <= 6) {
            return valor.replace(/(\d{2})(\d+)/, '($1) $2');
        }

        if (valor.length <= 10) {
            return valor.replace(/(\d{2})(\d{4})(\d+)/, '($1) $2-$3');
        }

        return valor.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    }

    function aplicarMascaraCep(valor) {
        valor = apenasNumeros(valor).slice(0, 8);

        if (valor.length <= 5) {
            return valor;
        }

        return valor.replace(/(\d{5})(\d{1,3})/, '$1-$2');
    }

    function toggleNumero() {
        if (!semNumeroCheckbox || !numeroInput) {
            return;
        }

        if (semNumeroCheckbox.checked) {
            numeroInput.value = 'S/N';
            numeroInput.readOnly = true;
        } else {
            if (numeroInput.value === 'S/N') {
                numeroInput.value = '';
            }

            numeroInput.readOnly = false;
        }
    }

    cpfInputs.forEach(function (input) {
        input.value = aplicarMascaraCpf(input.value);

        input.addEventListener('input', function () {
            input.value = aplicarMascaraCpf(input.value);
        });
    });

    phoneInputs.forEach(function (input) {
        input.value = aplicarMascaraTelefone(input.value);

        input.addEventListener('input', function () {
            input.value = aplicarMascaraTelefone(input.value);
        });
    });

    cepInputs.forEach(function (input) {
        input.value = aplicarMascaraCep(input.value);

        input.addEventListener('input', function () {
            input.value = aplicarMascaraCep(input.value);
        });
    });

    if (semNumeroCheckbox) {
        semNumeroCheckbox.addEventListener('change', toggleNumero);
        toggleNumero();
    }

    if (cepInput) {
        cepInput.addEventListener('blur', async function () {
            const cep = apenasNumeros(this.value);

            if (cep.length !== 8) {
                return;
            }

            try {
                const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await res.json();

                if (!data.erro) {
                    document.getElementById('rua').value = data.logradouro || '';
                    document.getElementById('bairro').value = data.bairro || '';
                    document.getElementById('cidade').value = data.localidade || '';
                    document.getElementById('uf').value = data.uf || '';
                }
            } catch (err) {
                console.warn('Erro ao buscar o CEP:', err);
            }
        });
    }

    if (window.bootstrap && typeof bootstrap.Tooltip === 'function') {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');

        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    if (formPaciente) {
        formPaciente.addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            if (form.querySelector('#exigeNotaFiscal') && !form.querySelector('#exigeNotaFiscal').checked) {
                formData.set('exige_nota_fiscal', '0');
            }

            if (formData.has('cpf')) {
                formData.set('cpf', apenasNumeros(formData.get('cpf')));
            }

            if (formData.has('telefone')) {
                formData.set('telefone', apenasNumeros(formData.get('telefone')));
            }

            if (formData.has('telefone_contato_emergencia')) {
                formData.set('telefone_contato_emergencia', apenasNumeros(formData.get('telefone_contato_emergencia')));
            }

            if (formData.has('cep')) {
                formData.set('cep', apenasNumeros(formData.get('cep')));
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    let data = {};

                    try {
                        data = await response.json();
                    } catch (jsonError) {
                        data = {};
                    }

                    if (data.errors) {
                        const mensagens = Object.values(data.errors).flat().join('<br>');

                        Swal.fire({
                            icon: 'error',
                            title: 'Erro de validação',
                            html: mensagens
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: data.message || 'Erro ao salvar paciente.'
                        });
                    }
                } else {
                    const data = await response.json();

                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: data.message || 'Paciente salvo com sucesso!'
                    }).then(() => {
                        window.location.href = "{{ route('pacientes.index') }}";
                    });
                }

            } catch (error) {
                console.error(error);

                Swal.fire({
                    icon: 'error',
                    title: 'Erro inesperado',
                    text: 'Tente novamente mais tarde.'
                });
            } finally {
                if (typeof hideSpinner === 'function') {
                    hideSpinner();
                }
            }
        });
    }
});
</script>
@endsection