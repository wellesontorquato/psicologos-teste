@extends('layouts.app')

@section('title', 'Agenda | PsiGestor')

@section('styles')
<style>
    /* Card do calendário */
    #calendar-card {
        border: 1px solid #dee2e6;
        border-radius: 12px;
        background: white;
        padding: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    /* Estilo dos eventos */
    .fc-event {
        border-radius: 6px !important;
        padding: 4px !important;
        font-size: 0.9rem !important;
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
    .fc .fc-event.evento-apos-meia-noite {
        background: #e3f7ff !important;
        border: 1px dashed #00c4ff !important;
        color: #006f99 !important;
        font-weight: 600 !important;
    }
    .fc-daygrid-event-dot { display: none !important; }

    /* Cabeçalho customizado */
    .calendar-header {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    @media (min-width: 768px) {
        .calendar-header {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    /* Título responsivo */
    .calendar-title {
        font-weight: 700;
        color: #222;
        display: flex;
        align-items: center;
        gap: 10px;
        line-height: 1.2;
    }
    .calendar-title i {
        font-size: 1.8rem;
        color: #00aaff;
    }
    .calendar-title span {
        display: flex;
        flex-direction: column;
    }
    .calendar-title .main-title {
        font-size: 1.2rem;
        color: #111;
    }
    .calendar-title .sub-title {
        font-size: 0.95rem;
        color: #666;
        font-weight: 500;
    }

    @media (min-width: 992px) {
        .calendar-title .main-title {
            font-size: 1.6rem;
        }
        .calendar-title .sub-title {
            font-size: 1.1rem;
        }
    }
    @media (max-width: 991px) {
        .calendar-title .main-title {
            font-size: 1.4rem;
        }
    }
    @media (max-width: 576px) {
        .calendar-title {
            justify-content: center;
            text-align: center;
        }
        .calendar-title .main-title {
            font-size: 1.2rem;
        }
        .calendar-title .sub-title {
            font-size: 1rem;
        }
    }

    /* Botões */
    .calendar-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }
    .calendar-controls button {
        border-radius: 8px !important;
        font-size: 0.9rem !important;
        padding: 6px 14px !important;
        border: 1px solid #ccc;
        background: #f8f9fa;
        color: #333;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
        flex: 1 1 auto; /* Ajuste para dividir espaço igualmente */
        min-width: 90px;
    }
    .calendar-controls button:hover {
        background: #e9ecef;
        color: #000;
    }
    .calendar-controls button.active {
        background: #00aaff !important;
        color: #fff !important;
        border-color: #0090d0 !important;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0, 170, 255, 0.35);
    }

    /* Mobile */
    @media (max-width: 576px) {
        .calendar-controls {
            flex-wrap: nowrap;
            overflow-x: auto;
            justify-content: center;
        }
        .calendar-controls button {
            flex: 0 0 auto;
            min-width: 90px;
        }
    }
</style>


@section('content')
<div class="container">

    {{-- Cabeçalho clean --}}
    <div class="calendar-header bg-white p-3 rounded shadow-sm d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="calendar-title">
            <i class="bi bi-calendar3 text-primary fs-3"></i>
            <span id="calendarTitle" class="fw-bold">Minha Agenda</span>
        </div>
        <div class="calendar-controls d-flex flex-wrap gap-2">
            <button id="prevBtn" class="btn btn-outline-primary px-3">←</button>
            <button id="todayBtn" class="btn btn-outline-secondary px-3">Hoje</button>
            <button id="nextBtn" class="btn btn-outline-primary px-3">→</button>
            <button id="monthBtn" class="btn btn-primary active px-3">Mês</button>
            <button id="weekBtn" class="btn btn-outline-primary px-3">Semana</button>
            <button id="dayBtn" class="btn btn-outline-primary px-3">Dia</button>
        </div>
    </div>

    {{-- Calendário --}}
    <div id="calendar-card">
        <div id="calendar"></div>
    </div>
</div>

<!-- Modal Sessão -->
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
<!-- FullCalendar + SweetAlert -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendarTitle = document.getElementById('calendarTitle');
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
        plugins: [
            window.FullCalendar.dayGridPlugin,
            window.FullCalendar.timeGridPlugin,
            window.FullCalendar.interactionPlugin,
            window.FullCalendar.bootstrap5Plugin
        ],
        themeSystem: 'bootstrap5',
        timeZone: 'local',
        height: 650,
        locale: window.FullCalendar.ptBr,
        initialView: 'dayGridMonth',
        headerToolbar: false, // desabilita header nativo
        events: '/api/sessoes',

        datesSet: function(info) {
            calendarTitle.innerText = "Minha Agenda - " + info.view.title;
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
            const horaFim = end ? formatHora(end) : '';
            const titulo = event.title;

            if (viewType === 'dayGridMonth') {
                return {
                    html: `<div>${horaInicio} - ${titulo}</div>`
                };
            } else {
                return {
                    html: `<div>${horaInicio} - ${horaFim}</div><div>${titulo}</div>`
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

        eventDidMount: function (info) {
            const tooltip = info.event.extendedProps.tooltip;
            if (tooltip) {
                info.el.setAttribute('title', tooltip);
            }
        }
    });

    calendar.render();

        // Botões customizados
        document.getElementById('prevBtn').onclick = () => calendar.prev();
        document.getElementById('nextBtn').onclick = () => calendar.next();
        document.getElementById('todayBtn').onclick = () => calendar.today();

        // Views (Mês / Semana / Dia) com controle de active
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
                document.querySelectorAll('.calendar-controls button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });

    function abrirModalCriar(data) {
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
