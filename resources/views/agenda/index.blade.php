@extends('layouts.app')

@section('title', 'Agenda | PsiGestor')

@section('styles')
<style>
    /* Garante prioridade absoluta sobre o Tailwind */
    body .fc .fc-event.evento-pago {
        background-color: #28a745 !important;
        border: 2px solid #1e7e34 !important;
        color: white !important;
        font-weight: bold !important;
    }

    body .fc .fc-event.evento-pendente {
        background-color: #dc3545 !important;
        border: 2px solid #a71d2a !important;
        color: white !important;
        font-weight: bold !important;
    }

    body .fc .fc-event.evento-apos-meia-noite {
        background-color: rgba(0, 174, 255, 0.15) !important;
        border: 2px dashed #00c4ff !important;
        color: #005f88 !important;
        font-weight: bold !important;
    }

    /* Remove a bolinha azul padrão do evento */
    .fc-daygrid-event-dot {
        display: none !important;
    }
</style>
@endsection

@section('content')
<div class="container">
    <h2 class="mb-4">Agenda</h2>
    <div id="calendar"></div>
</div>

<!-- Modal de Sessão -->
<div class="modal fade" id="modalSessao" tabindex="-1" aria-labelledby="modalSessaoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formSessao">
      @csrf
      <input type="hidden" name="id" id="sessao_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitulo">Nova Sessão</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
              <label>Paciente</label>
              <select name="paciente_id" id="paciente_id" class="form-control" required>
                  @foreach(\App\Models\Paciente::where('user_id', auth()->id())->get() as $paciente)
                      <option value="{{ $paciente->id }}">{{ $paciente->nome }}</option>
                  @endforeach
              </select>
          </div>
          <div class="mb-3">
              <label>Data e Hora</label>
              <input type="datetime-local" name="data_hora" id="data_hora" class="form-control" required>
          </div>
          <div class="mb-3">
              <label>Valor</label>
              <input type="number" step="0.01" name="valor" id="valor" class="form-control" required>
          </div>
          <div class="mb-3">
              <label>Duração (minutos)</label>
              <input type="number" name="duracao" id="duracao" class="form-control" value="50" required>
          </div>
          <div class="mb-3 form-check">
              <input type="checkbox" name="foi_pago" id="foi_pago" class="form-check-input">
              <label class="form-check-label" for="foi_pago">Foi pago?</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="btnSalvar">Salvar</button>
        </div>
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
        timezone: false,
        height: 650,
        locale: window.FullCalendar.ptBr,
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        events: '/api/sessoes',

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
                campos.data_hora.value = sessao.data_hora.slice(0, 16);
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
            });

        } catch (error) {
            Swal.fire('Erro', error.message || 'Erro inesperado ao salvar a sessão.', 'error');
        }
    });
});
</script>
@endsection
