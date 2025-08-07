@extends('layouts.app')

@section('title', 'Editar Paciente | PsiGestor')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Paciente</h2>
    <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Voltar para lista de pacientes
    </a>

    <form id="form-paciente" action="{{ route('pacientes.update', $paciente) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Identificação --}}
        <div class="row g-3">
            <div class="col-md-8">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" value="{{ $paciente->nome }}" required>
            </div>
            <div class="col-md-4">
                <label>Data de Nascimento</label>
                <input type="date" name="data_nascimento" class="form-control" value="{{ $paciente->data_nascimento }}">
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-4">
                <label>Sexo</label>
                <select name="sexo" class="form-control">
                    <option value="">Selecione</option>
                    <option value="M" {{ $paciente->sexo === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ $paciente->sexo === 'F' ? 'selected' : '' }}>Feminino</option>
                    <option value="Outro" {{ $paciente->sexo === 'Outro' ? 'selected' : '' }}>Outro</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Telefone</label>
                <input type="text" name="telefone" class="form-control" value="{{ $paciente->telefone }}">
            </div>
            <div class="col-md-4">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ $paciente->email }}">
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-6">
                <label>CPF</label>
                <input type="text" name="cpf" class="form-control" value="{{ $paciente->cpf }}" placeholder="000.000.000-00">
            </div>
            <div class="col-md-6 d-flex align-items-end flex-column">
                {{-- Tooltip explicativo --}}
                <small 
                    class="text-muted mb-2 d-block" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Este site não realiza a emissão direta de notas fiscais pelo Receita Saúde. O processo deve ser concluído manualmente pelo profissional.">
                    ℹ️ Informativo sobre Receita Saúde
                </small>
                {{-- Switch atualizado --}}
                <input type="hidden" name="exige_nota_fiscal" value="0">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="exige_nota_fiscal" id="exige_nota_fiscal" value="1" {{ $paciente->exige_nota_fiscal ? 'checked' : '' }}>
                    <label class="form-check-label" for="exige_nota_fiscal">Emissão de Nota Fiscal Receita Saúde</label>
                </div>
            </div>
        </div>

        {{-- Contato de Emergência --}}
        <hr class="my-4">
        <h5>Contato de Emergência</h5>

        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <label>Nome do Contato</label>
                <input type="text" name="nome_contato_emergencia" class="form-control" placeholder="Nome completo" value="{{ $paciente->nome_contato_emergencia }}">
            </div>
            <div class="col-md-3">
                <label>Telefone do Contato</label>
                <input type="text" name="telefone_contato_emergencia" class="form-control" placeholder="(00) 00000-0000" value="{{ $paciente->telefone_contato_emergencia }}">
            </div>
            <div class="col-md-3">
                <label>Parentesco</label>
                <select name="parentesco_contato_emergencia" class="form-control">
                    <option value="">Selecione</option>
                    <option value="Pai" {{ $paciente->parentesco_contato_emergencia === 'Pai' ? 'selected' : '' }}>Pai</option>
                    <option value="Mãe" {{ $paciente->parentesco_contato_emergencia === 'Mãe' ? 'selected' : '' }}>Mãe</option>
                    <option value="Cônjuge" {{ $paciente->parentesco_contato_emergencia === 'Cônjuge' ? 'selected' : '' }}>Cônjuge</option>
                    <option value="Filho(a)" {{ $paciente->parentesco_contato_emergencia === 'Filho(a)' ? 'selected' : '' }}>Filho(a)</option>
                    <option value="Irmão(ã)" {{ $paciente->parentesco_contato_emergencia === 'Irmão(ã)' ? 'selected' : '' }}>Irmão(ã)</option>
                    <option value="Amigo(a)" {{ $paciente->parentesco_contato_emergencia === 'Amigo(a)' ? 'selected' : '' }}>Amigo(a)</option>
                    <option value="Outro" {{ $paciente->parentesco_contato_emergencia === 'Outro' ? 'selected' : '' }}>Outro</option>
                </select>
            </div>
        </div>

        {{-- Endereço --}}
        <hr class="my-4">
        <h5>Endereço</h5>

        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <label>CEP</label>
                <input type="text" name="cep" id="cep" class="form-control" value="{{ $paciente->cep }}">
            </div>
            <div class="col-md-5">
                <label>Rua</label>
                <input type="text" name="rua" id="rua" class="form-control" value="{{ $paciente->rua }}">
            </div>
            <div class="col-md-2">
                <label>Número</label>
                <input type="text" name="numero" id="numero" class="form-control" value="{{ $paciente->numero }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="sem_numero" id="sem_numero" {{ $paciente->numero === 'S/N' ? 'checked' : '' }}>
                    <label class="form-check-label" for="sem_numero">(S/N)</label>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label>Complemento</label>
                <input type="text" name="complemento" class="form-control" value="{{ $paciente->complemento }}">
            </div>
            <div class="col-md-4">
                <label>Bairro</label>
                <input type="text" name="bairro" id="bairro" class="form-control" value="{{ $paciente->bairro }}">
            </div>
            <div class="col-md-3">
                <label>Cidade</label>
                <input type="text" name="cidade" id="cidade" class="form-control" value="{{ $paciente->cidade }}">
            </div>
            <div class="col-md-1">
                <label>UF</label>
                <input type="text" name="uf" id="uf" class="form-control" value="{{ $paciente->uf }}" maxlength="2">
            </div>
        </div>

        {{-- Complementares --}}
        <hr class="my-4">
        <div class="mb-3">
            <label>Observações</label>
            <textarea name="observacoes" class="form-control" rows="3">{{ $paciente->observacoes }}</textarea>
        </div>

        <div class="mb-3">
            <label>Nova Medicação</label>
            <input type="text" name="nova_medicacao" class="form-control" placeholder="Ex: Fluoxetina 20mg">
            <small class="form-text text-muted">Se informado, será registrado no histórico como nova medicação.</small>
        </div>

        {{-- Botões --}}
        <div class="d-flex gap-3 mt-4">
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('pacientes.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// CEP para endereço automático
document.getElementById('cep').addEventListener('blur', async function () {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length !== 8) return;

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

// Marcar número como S/N
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('sem_numero');
    const numeroInput = document.getElementById('numero');

    function toggleNumero() {
        if (checkbox.checked) {
            numeroInput.value = 'S/N';
            numeroInput.readOnly = true;
        } else {
            if (numeroInput.value === 'S/N') {
                numeroInput.value = '';
            }
            numeroInput.readOnly = false;
        }
    }

    checkbox.addEventListener('change', toggleNumero);
    toggleNumero();
});

// Envio assíncrono do formulário com feedback
document.getElementById('form-paciente').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    formData.append('_method', 'PUT'); // Laravel precisa disso

    // Corrige o envio do campo exige_nota_fiscal (caso desmarcado)
    if (!form.querySelector('#exige_nota_fiscal').checked) {
        formData.set('exige_nota_fiscal', '0');
    }

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });

        if (!response.ok) {
            const data = await response.json();

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
                    text: data.message || 'Erro ao atualizar paciente.'
                });
            }
        } else {
            const data = await response.json();
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: data.message || 'Paciente atualizado com sucesso!'
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
    }

    finally {
        hideSpinner(); // <-- Garante que o spinner é desligado SEMPRE no final
    }
});

// Ativar tooltips do Bootstrap
document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection
