@extends('layouts.app')

@section('title', 'Agenda | PsiGestor')

@section('styles')
<style>
    /* Variáveis de Cor para facilitar a customização */
    :root {
        --psi-primary: #007bff; /* Azul primário */
        --psi-primary-dark: #0056b3;
        --psi-light: #f8f9fa;
        --psi-border: #dee2e6;
        --psi-shadow: rgba(0, 0, 0, 0.06);
        --psi-text-dark: #343a40;
        --psi-text-light: #6c757d;
    }

    /* Cabeçalho Moderno */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap; /* Permite que os itens quebrem para a linha de baixo em telas pequenas */
        gap: 1.5rem; /* Espaçamento entre os blocos (título e controles) */
        padding: 1rem 1.5rem;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px var(--psi-shadow);
        margin-bottom: 2rem;
        border: 1px solid var(--psi-border);
    }

    /* Bloco do Título */
    .header-title {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }
    .header-title .icon-wrapper {
        background-color: var(--psi-light);
        color: var(--psi-primary);
        font-size: 1.5rem;
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: grid;
        place-items: center;
    }
    .header-title .title-text .main-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--psi-text-dark);
        line-height: 1.2;
    }
    .header-title .title-text .sub-title {
        font-size: 0.9rem;
        color: var(--psi-text-light);
    }

    /* Bloco de Controles (botões) */
    .header-controls {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem; /* Espaçamento entre os grupos de botões */
    }

    /* Estilo para os botões de navegação e visualização */
    .btn-group .btn {
        border-radius: 6px !important;
        transition: all 0.2s ease;
    }
    .btn-group .btn.active {
        background-color: var(--psi-primary) !important;
        border-color: var(--psi-primary) !important;
        color: #fff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.4);
    }
    .btn-group .btn:not(.active):hover {
        background-color: #e9ecef;
    }

    /* Responsividade para o cabeçalho */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        .header-controls {
            flex-direction: column;
            align-items: stretch;
        }
        .header-controls .btn-group, .header-controls .btn {
            width: 100%;
        }
    }

    /* Estilo do Calendário (mantido) */
    #calendar-card {
        border: 1px solid var(--psi-border);
        border-radius: 12px;
        background: white;
        padding: 1rem;
        box-shadow: 0 2px 6px var(--psi-shadow);
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
<div class="container">

    <header class="page-header">
        <div class="header-title">
            <div class="icon-wrapper">
                <i class="bi bi-calendar-week"></i>
            </div>
            <div class="title-text">
                <h1 id="calendarTitle" class="main-title mb-0">Minha Agenda</h1>
                <div class="sub-title">
                    @if(auth()->user()?->google_connected)
                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill">
                            <i class="bi bi-google me-1"></i>Google Agenda Conectado
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill">
                            <i class="bi bi-google me-1"></i>Não conectado
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="header-controls">
            @if(auth()->user()?->google_connected)
                <div class="btn-group">
                    <a href="{{ route('google.connect') }}" class="btn btn-outline-secondary btn-sm" title="Reautenticar com o Google">
                        <i class="bi bi-arrow-repeat"></i>
                    </a>
                    <form action="{{ route('google.disconnect') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja desconectar do Google Agenda?')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Desconectar do Google">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('google.connect') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-google me-1"></i> Conectar ao Google
                </a>
            @endif

            <div class="btn-group" role="group">
                <button id="prevBtn" class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-left"></i></button>
                <button id="todayBtn" class="btn btn-outline-secondary btn-sm">Hoje</button>
                <button id="nextBtn" class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-right"></i></button>
            </div>

            <div class="btn-group" role="group">
                <button id="monthBtn" class="btn btn-outline-secondary btn-sm active">Mês</button>
                <button id="weekBtn" class="btn btn-outline-secondary btn-sm">Semana</button>
                <button id="dayBtn" class="btn btn-outline-secondary btn-sm">Dia</button>
            </div>
        </div>
    </header>

    {{-- Calendário --}}
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
{{-- Seus scripts permanecem os mesmos --}}
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendarTitleEl = document.getElementById('calendarTitle'); // Renomeei para evitar conflito
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

    if (!window.FullCalendar || !calendarEl) return;

    const calendar = new window.FullCalendar.Calendar(calendarEl, {
        // ... (resto do seu JS do FullCalendar, que não precisa mudar)
        plugins: [
            window.FullCalendar.dayGridPlugin,
            window.FullCalendar.timeGridPlugin,
            window.FullCalendar.interactionPlugin,
            window.FullCalendar.bootstrap5Plugin
        ],
        themeSystem: 'bootstrap5',
        timeZone: 'local',
        height: 650,
        locale: 'pt-br', // Alterado para o código de localidade correto
        initialView: 'dayGridMonth',
        headerToolbar: false, // Mantém o header nativo desabilitado
        events: '/api/sessoes',

        datesSet: function(info) {
            // Atualiza o título dinamicamente
            calendarTitleEl.innerText = info.view.title;
        },

        // ... (O resto do seu código JS, como eventContent, dateClick, eventClick, etc., continua aqui)
        eventContent: function (arg) {
            const viewType = arg.view.type;
            const event = arg.event;
            const start = event.start;
            const end = event.end;
            const formatHora = (date) =>
                date.getHours().toString().padStart(2, '0') + ':' +
                date.getMinutes().toString().padStart(2, '0');
            const horaInicio = formatHora(start);
            const horaFim = end ? formatHora(end) : '';
            const titulo = event.title;

            if (viewType === 'dayGridMonth') {
                return { html: `<div>${horaInicio} - ${titulo}</div>` };
            } else {
                return { html: `<div>${horaInicio} - ${horaFim}</div><div>${titulo}</div>` };
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
        eventDidMount: function (info) {
            const tooltip = info.event.extendedProps.tooltip;
            if (tooltip) {
                info.el.setAttribute('title', tooltip);
            }
        }
    });

    calendar.render();

    // Botões customizados (o JS continua funcionando pois os IDs foram mantidos)
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
            document.querySelectorAll('.header-controls .btn-group .btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    function abrirModalCriar(data) {
        // ... (código do modal)
        campos.id.value = '';
        campos.paciente.selectedIndex = 0;
        campos.data_hora.value = data.length <= 10 ? data + 'T09:00' : data;
        campos.valor.value = '';
        campos.duracao.value = 50;
        campos.foi_pago.checked = false;
        campos.titulo.innerText = "Nova Sessão";
        modal.show();
    }

    document.getElementById('formSessao').addEventListener('submit', async function (e) {
        // ... (código do submit do form)
        e.preventDefault();

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
                    Swal.fire({
                        icon: 'error',
                        title: 'Conflito de Horário',
                        text: resData.message,
                        confirmButtonColor: '#3085d6',
                    });
                } else {
                    Swal.fire('Erro', resData.message || 'Erro ao salvar a sessão.', 'error');
                }

                if (typeof hideSpinner === 'function') hideSpinner();
                return;
            }

            modal.hide();
            calendar.refetchEvents();

            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: id ? 'Sessão atualizada com sucesso!' : 'Sessão criada com sucesso!',
                timer: 1800,
                showConfirmButton: false
            }).then(() => {
                if (typeof hideSpinner === 'function') hideSpinner();
            });

        } catch (error) {
            Swal.fire('Erro', error.message || 'Erro inesperado ao salvar a sessão.', 'error')
                .then(() => {
                    if (typeof hideSpinner === 'function') hideSpinner();
                });
        }
    });
});
</script>
@endsection