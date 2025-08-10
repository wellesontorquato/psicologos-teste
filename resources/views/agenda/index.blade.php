@extends('layouts.app')

@section('title', 'Agenda | PsiGestor')

@section('styles')
<style>
    /* ==================================================================
      NOVOS ESTILOS - Baseado na imagem e no código original
    ==================================================================
    */

    /* Reset e Layout Principal */
    .page-container {
        /* Usamos um container próprio para o cabeçalho e agenda */
    }
    
    .page-header {
        margin-bottom: 1.5rem;
    }

    /* Título principal com ícone customizado */
    .title-wrapper {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .title-icon {
        width: 52px;
        height: 52px;
        border: 1px solid #ced4da;
        border-radius: 8px;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .title-icon .month {
        background-color: #dc3545; /* Vermelho */
        color: white;
        text-align: center;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 2px 0;
        line-height: 1.2;
        text-transform: uppercase;
    }
    .title-icon .day {
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 500;
        color: #343a40;
    }
    
    #calendarPageTitle {
        font-size: 2rem;
        font-weight: 600;
        color: #343a40;
    }

    /* Controles (Google e Navegação) */
    .controls-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .google-controls, .calendar-controls {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    /* Estilo padrão dos botões para ser fiel à imagem */
    .btn-control {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #495057;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
    }
    .btn-control:hover {
        background-color: #e9ecef;
        border-color: #ced4da;
    }
    .btn-control.active {
        background-color: #495057;
        color: #fff;
        border-color: #495057;
    }
    .btn-control i {
        vertical-align: middle;
    }


    /* ESTILOS ORIGINAIS MANTIDOS PARA O CALENDÁRIO */
    #calendar-card {
        border: 1px solid #dee2e6;
        border-radius: 12px;
        background: white;
        padding: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.07);
    }
    .fc-event {
        border-radius: 6px !important;
        padding: 4px !important;
        font-size: 0.9rem !important;
        cursor: pointer;
    }
    .fc .fc-event.evento-pago {
        background: linear-gradient(90deg, #28a745, #218838) !important;
        border: none !important;
        color: white !important;
        font-weight: 600 !important;
    }
    .fc .fc-event.evento-pendente {
        background: linear-gradient(90deg, #dc3545, #a71d2a) !important;
        border: none !important;
        color: white !important;
        font-weight: 600 !important;
    }
    .fc-daygrid-event-dot { display: none !important; }
</style>
@endsection

@section('content')
<div class="container page-container">

    <div class="page-header">
        <div class="title-wrapper">
            <div class="title-icon">
                <div class="month">AGO</div>
                <div class="day">10</div>
            </div>
            <h1 id="calendarPageTitle">Minha Agenda</h1>
        </div>

        <div class="controls-wrapper">
            <div class="google-controls">
                @if(auth()->user()?->google_connected)
                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill me-2">
                        <i class="bi bi-google me-1"></i> Google Agenda Conectado
                    </span>
                    <a href="{{ route('google.connect') }}" class="btn btn-control btn-sm">
                        <i class="bi bi-arrow-repeat me-1"></i> Reautenticar
                    </a>
                    <form action="{{ route('google.disconnect') }}" method="POST" onsubmit="return confirm('Desconectar do Google Agenda?')">
                        @csrf
                        <button type="submit" class="btn btn-control btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Desconectar
                        </button>
                    </form>
                @else
                    <a href="{{ route('google.connect') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-google me-1"></i> Conectar ao Google
                    </a>
                @endif
            </div>

            <div class="calendar-controls">
                <div class="btn-group">
                    <button id="prevBtn" class="btn btn-control btn-sm"><i class="bi bi-chevron-left"></i></button>
                    <button id="todayBtn" class="btn btn-control btn-sm">Hoje</button>
                    <button id="nextBtn" class="btn btn-control btn-sm"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div class="btn-group">
                    <button id="monthBtn" class="btn btn-control btn-sm active">Mês</button>
                    <button id="weekBtn" class="btn btn-control btn-sm">Semana</button>
                    <button id="dayBtn" class="btn btn-control btn-sm">Dia</button>
                </div>
            </div>
        </div>
    </div>

    <div id="calendar-card">
        <div id="calendar"></div>
    </div>
</div>

<div class="modal fade" id="modalSessao" tabindex="-1" aria-labelledby="modalSessaoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formSessao" class="modal-content shadow-sm">
      @csrf
      <input type="hidden" name="id" id="sessao_id">
      <div class="modal-header">
        <h5 class="modal-title fw-semibold" id="modalTitulo">Nova Sessão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label small text-muted fw-semibold">Paciente</label>
                <select name="paciente_id" id="paciente_id" class="form-select form-select-sm shadow-sm" required>
                    @foreach(\App\Models\Paciente::where('user_id', auth()->id())->get() as $paciente)
                        <option value="{{ $paciente->id }}">{{ $paciente->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small text-muted fw-semibold">Data e Hora</label>
                <input type="datetime-local" name="data_hora" id="data_hora" 
                       class="form-control form-control-sm shadow-sm" required>
            </div>
            <div class="col-6">
                <label class="form-label small text-muted fw-semibold">Valor (R$)</label>
                <input type="number" step="0.01" name="valor" id="valor" 
                       class="form-control form-control-sm shadow-sm" required>
            </div>
            <div class="col-6">
                <label class="form-label small text-muted fw-semibold">Duração (min)</label>
                <input type="number" name="duracao" id="duracao" 
                       class="form-control form-control-sm shadow-sm" value="50" required>
            </div>
            <div class="col-12 form-check">
                <input type="checkbox" name="foi_pago" id="foi_pago" class="form-check-input">
                <label class="form-check-label small fw-semibold" for="foi_pago">Foi Pago?</label>
            </div>
        </div>
      </div>
      <div class="modal-footer d-flex flex-column flex-md-row gap-2">
        <button type="button" class="btn btn-secondary w-100 w-md-auto" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success w-100 w-md-auto">Salvar</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/pt-br.global.min.js'></script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    // MODIFICAÇÃO 1: Pegando o novo elemento do título
    const calendarPageTitle = document.getElementById('calendarPageTitle');
    const modal = new bootstrap.Modal(document.getElementById('modalSessao'));

    const campos = {
        id: document.getElementById('sessao_id'),
        paciente: document.getElementById('paciente_id'),
        data_hora: document.getElementById('data_hora'),
        valor: document.getElementById('valor'),
        duracao: document.getElementById('duracao'),
        foi_pago: document.getElementById('foi_pago'),
        titulo: document.getElementById('modalTitulo'),
    };

    // Verificação de segurança, a causa provável do erro anterior
    if (!window.FullCalendar || !calendarEl) {
        console.error("FullCalendar não foi carregado ou o elemento #calendar não foi encontrado.");
        return;
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        // Renomeei 'plugins' para a chave correta 'locales' para pt-br
        locale: 'pt-br',
        themeSystem: 'bootstrap5',
        timeZone: 'local',
        height: 650,
        initialView: 'dayGridMonth',
        headerToolbar: false, // desabilita header nativo
        events: '/api/sessoes',

        datesSet: function(info) {
            // MODIFICAÇÃO 2: Atualizando o texto do novo título
            if(calendarPageTitle) {
                calendarPageTitle.innerText = info.view.title;
            }
        },

        eventContent: function (arg) {
            const viewType = arg.view.type;
            const event = arg.event;
            const start = event.start;
            const end = event.end;
            const formatHora = (date) =>
                date.getHours().toString().padStart(2, '0') + ':' +
                date.getMinutes().toString().padStart(2, '0');

            const horaInicio = formatHora(start);
            const titulo = event.title;

            if (viewType === 'dayGridMonth') {
                return {
                    html: `<div>${horaInicio} - ${titulo}</div>`
                };
            } else {
                return {
                    html: `<div>${horaInicio} - ${end ? formatHora(end) : ''}</div><div>${titulo}</div>`
                };
            }
        },

        dateClick: function (info) {
            abrirModalCriar(info.dateStr);
        },

        eventClick: async function (info) {
            info.jsEvent.preventDefault();
            const id = info.event.id;

            try {
                const res = await fetch(`/sessoes-json/${id}`);
                if (!res.ok) throw new Error();

                const sessao = await res.json();

                campos.id.value = sessao.id;
                campos.paciente.value = sessao.paciente_id;
                campos.data_hora.value = sessao.data_hora;
                campos.valor.value = sessao.valor;
                campos.duracao.value = sessao.duracao;
                campos.foi_pago.checked = sessao.foi_pago == true;
                campos.titulo.innerText = "Editar Sessão";

                modal.show();
            } catch {
                Swal.fire('Erro', 'Erro ao carregar dados da sessão.', 'error');
            }
        },
    });

    calendar.render();

    // Botões customizados (seu código original, funcionando com os novos botões)
    document.getElementById('prevBtn').onclick = () => calendar.prev();
    document.getElementById('nextBtn').onclick = () => calendar.next();
    document.getElementById('todayBtn').onclick = () => calendar.today();

    const viewButtons = {
        monthBtn: 'dayGridMonth',
        weekBtn: 'timeGridWeek',
        dayBtn: 'timeGridDay'
    };

    Object.keys(viewButtons).forEach(id => {
        const btn = document.getElementById(id);
        btn.addEventListener('click', () => {
            calendar.changeView(viewButtons[id]);

            // Remove active dos outros e adiciona no clicado
            document.querySelectorAll('.calendar-controls .btn-control').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    function abrirModalCriar(data) {
        campos.id.value = '';
        document.getElementById('formSessao').reset(); // Melhor forma de limpar o form
        campos.data_hora.value = data.length <= 10 ? data + 'T09:00' : data;
        campos.duracao.value = 50;
        campos.titulo.innerText = "Nova Sessão";
        modal.show();
    }

    document.getElementById('formSessao').addEventListener('submit', async function (e) {
        e.preventDefault();
        // ... (seu código de submit, sem alterações)
        const id = campos.id.value;
        const rota = id ? `/sessoes-json/${id}` : `/sessoes-json`;
        const metodo = id ? 'PUT' : 'POST';
        const payload = {
            paciente_id: campos.paciente.value,
            data_hora: campos.data_hora.value,
            valor: campos.valor.value,
            duracao: parseInt(campos.duracao.value),
            foi_pago: campos.foi_pago.checked ? 1 : 0,
        };
        try {
            const response = await fetch(rota, {
                method: metodo,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });
            const resData = await response.json();
            if (!response.ok) {
                if (resData?.message?.includes("Conflito de horário")) {
                    Swal.fire({ icon: 'error', title: 'Conflito de Horário', text: resData.message });
                } else {
                    Swal.fire('Erro', resData.message || 'Erro ao salvar a sessão.', 'error');
                }
                return;
            }
            modal.hide();
            calendar.refetchEvents();
            Swal.fire({ icon: 'success', title: 'Sucesso!', text: id ? 'Sessão atualizada!' : 'Sessão criada!', timer: 1800, showConfirmButton: false });
        } catch (error) {
            Swal.fire('Erro', error.message || 'Erro inesperado.', 'error');
        }
    });
});
</script>
@endsection