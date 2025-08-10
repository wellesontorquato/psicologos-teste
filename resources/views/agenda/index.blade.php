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

    /* Estilo dos eventos (sessões) */
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

    /* Eventos de fundo (finais de semana & feriados) */
    .bg-weekend {
        background-color: rgba(0, 0, 0, 0.035) !important;
    }
    .bg-feriado {
        background: repeating-linear-gradient(
            -45deg,
            rgba(255, 193, 7, 0.20),
            rgba(255, 193, 7, 0.20) 6px,
            rgba(255, 193, 7, 0.30) 6px,
            rgba(255, 193, 7, 0.30) 12px
        ) !important;
    }

    /* Cabeçalho customizado */
    .calendar-header {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 20px;
        padding: 12px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    @media (min-width: 768px) {
        .calendar-header {
            grid-template-columns: 1.2fr auto 1fr;
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
        min-height: 42px;
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
        .calendar-title .main-title { font-size: 1.6rem; }
        .calendar-title .sub-title { font-size: 1.05rem; }
    }
    @media (max-width: 991px) {
        .calendar-title .main-title { font-size: 1.4rem; }
    }
    @media (max-width: 576px) {
        .calendar-title { justify-content: center; text-align: center; }
        .calendar-title .main-title { font-size: 1.2rem; }
        .calendar-title .sub-title { font-size: 1rem; }
    }

    /* Barra de ações (botões) — visual alinhado */
    .calendar-actions {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
    }
    @media (min-width: 768px) {
        .calendar-actions {
            grid-template-columns: repeat(4, auto);
            justify-content: center;
            align-items: center;
        }
    }
    .calendar-actions .btn {
        min-height: 42px;
        border-radius: 8px !important;
        font-size: 0.95rem !important;
        padding: 6px 14px !important;
        font-weight: 500;
    }

    /* Controles de navegação/visualização */
    .calendar-controls {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 8px;
        align-items: center;
    }
    @media (min-width: 768px) {
        .calendar-controls {
            grid-template-columns: repeat(6, auto);
            justify-content: end;
        }
    }
    .calendar-controls .btn {
        min-height: 42px;
        border-radius: 8px !important;
        font-size: 0.95rem !important;
        padding: 6px 14px !important;
        font-weight: 500;
    }
    .calendar-controls .btn.active {
        background: #00aaff !important;
        color: #fff !important;
        border-color: #0090d0 !important;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0, 170, 255, 0.35);
    }
</style>
@endsection

@section('content')
<div class="container">

    {{-- Cabeçalho clean alinhado --}}
    <div class="calendar-header">
        <div class="calendar-title">
            <i class="bi bi-calendar3 text-primary fs-3"></i>
            <span class="d-flex flex-column">
                <span id="calendarTitle" class="fw-bold main-title">Minha Agenda</span>
                <span class="sub-title">
                    @if(auth()->user()?->google_connected)
                        <span class="badge bg-success-subtle text-success border border-success-subtle" title="Integração ativa">
                            <i class="bi bi-google me-1"></i> Google Agenda conectado
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" title="Conecte para sincronizar">
                            <i class="bi bi-google me-1"></i> Não conectado
                        </span>
                    @endif
                </span>
            </span>
        </div>

        <div class="calendar-actions">
            {{-- Botões de integração Google --}}
            @if(auth()->user()?->google_connected)
                <a href="{{ route('google.connect') }}" class="btn btn-outline-success w-100">
                    <i class="bi bi-arrow-repeat me-1"></i> Reautenticar
                </a>
                <form action="{{ route('google.disconnect') }}" method="POST" onsubmit="return confirm('Desconectar do Google Agenda?')" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-x-circle me-1"></i> Desconectar
                    </button>
                </form>
                <a href="{{ route('sessoes.create') }}" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle me-1"></i> Nova Sessão
                </a>
                <a href="{{ route('sessoes.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-list-ul me-1"></i> Ver lista
                </a>
            @else
                <a href="{{ route('google.connect') }}" class="btn btn-primary w-100">
                    <i class="bi bi-google me-1"></i> Conectar ao Google
                </a>
                <a href="{{ route('sessoes.create') }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-plus-circle me-1"></i> Nova Sessão
                </a>
                <a href="{{ route('sessoes.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-list-ul me-1"></i> Ver lista
                </a>
                <div class="d-none d-md-block"></div>
            @endif
        </div>

        <div class="calendar-controls">
            <button id="prevBtn" class="btn btn-outline-primary px-3"><i class="bi bi-chevron-left"></i></button>
            <button id="todayBtn" class="btn btn-outline-secondary px-3">Hoje</button>
            <button id="nextBtn" class="btn btn-outline-primary px-3"><i class="bi bi-chevron-right"></i></button>
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
        <button type="submit" id="btnSalvarSessao" class="btn btn-success w-100 w-md-auto">Salvar</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<!-- Bootstrap Icons (necessário p/ ícones bi-*) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />
<!-- FullCalendar JS (bundle global) + locales -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>

<!-- Bootstrap + SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendarTitle = document.getElementById('calendarTitle');
    const modal = new bootstrap.Modal(document.getElementById('modalSessao'));
    const btnSalvar = document.getElementById('btnSalvarSessao');

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

    /* ====== Feriados nacionais BR por ano (com datas móveis) ====== */
    function easterDate(year) {
        // algoritmo de Meeus/Jones/Butcher
        const a = year % 19;
        const b = Math.floor(year / 100);
        const c = year % 100;
        const d = Math.floor(b / 4);
        const e = b % 4;
        const f = Math.floor((b + 8) / 25);
        const g = Math.floor((b - f + 1) / 3);
        const h = (19*a + b - d - g + 15) % 30;
        const i = Math.floor(c / 4);
        const k = c % 4;
        const l = (32 + 2*e + 2*i - h - k) % 7;
        const m = Math.floor((a + 11*h + 22*l) / 451);
        const month = Math.floor((h + l - 7*m + 114) / 31); // 3=Março, 4=Abril
        const day = ((h + l - 7*m + 114) % 31) + 1;
        return new Date(Date.UTC(year, month - 1, day)); // UTC para ISO simples
    }

    function toISODate(d) {
        const y = d.getUTCFullYear();
        const m = String(d.getUTCMonth()+1).padStart(2, '0');
        const day = String(d.getUTCDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    }

    function addDays(d, days) {
        const nd = new Date(d.getTime());
        nd.setUTCDate(nd.getUTCDate() + days);
        return nd;
    }

    function feriadosBRPorAno(year) {
        const pascoa = easterDate(year);
        const carnavalTer = addDays(pascoa, -47);
        const sextaSanta = addDays(pascoa, -2);
        const corpusChristi = addDays(pascoa, 60);

        // fixos + móveis (principais nacionais)
        const fixos = [
            `${year}-01-01`, // Confraternização Universal
            `${year}-04-21`, // Tiradentes
            `${year}-05-01`, // Dia do Trabalho
            `${year}-09-07`, // Independência
            `${year}-10-12`, // Nossa Senhora Aparecida
            `${year}-11-02`, // Finados
            `${year}-11-15`, // Proclamação da República
            `${year}-12-25`, // Natal
        ];

        const moveis = [
            toISODate(carnavalTer),   // Carnaval (terça)
            toISODate(sextaSanta),    // Sexta-feira Santa
            toISODate(pascoa),        // Páscoa (domingo)
            toISODate(corpusChristi), // Corpus Christi
        ];

        return [...fixos, ...moveis];
    }

    /* ====== Fonte de eventos de fundo: finais de semana e feriados ====== */
    // Finais de semana recorrentes
    const weekendBackground = {
        events: [
            {
                daysOfWeek: [0,6],       // dom(0) e sáb(6)
                startTime: '00:00',
                endTime: '24:00',
                display: 'background',
                className: 'bg-weekend',
            }
        ]
    };

    // Feriados (carregados dinamicamente conforme a visão exibida)
    function buildHolidayBgEvents(viewStart, viewEnd) {
        // Vamos cobrir ano(s) da janela visível
        const years = new Set();
        const s = new Date(viewStart.valueOf());
        const e = new Date(viewEnd.valueOf());
        for (let y = s.getFullYear(); y <= e.getFullYear(); y++) years.add(y);

        const dates = [];
        years.forEach(y => dates.push(...feriadosBRPorAno(y)));

        return dates.map(iso => ({
            start: iso,
            end: iso,  // FullCalendar lida como all-day
            allDay: true,
            display: 'background',
            className: 'bg-feriado',
        }));
    }

    // Manter uma source dinâmica para feriados
    let feriadosSource = null;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [
            FullCalendar.dayGridPlugin,
            FullCalendar.timeGridPlugin,
            FullCalendar.interactionPlugin,
            FullCalendar.bootstrap5Plugin
        ],
        themeSystem: 'bootstrap5',
        timeZone: 'local',
        height: 650,
        locale: 'pt-br',
        initialView: 'dayGridMonth',
        headerToolbar: false, // desabilita header nativo
        events: '/api/sessoes',

        selectable: true,
        selectMirror: true,
        expandRows: true,
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00',

        // classes visuais conforme props
        eventClassNames(info) {
            const cx = [];
            const x = info.event.extendedProps || {};
            if (x.foi_pago) cx.push('evento-pago');
            if (!x.foi_pago) cx.push('evento-pendente');

            // Heurística de atravessar a meia-noite
            if (info.event.start && info.event.end) {
                const s = info.event.start; const e = info.event.end;
                if (s.toDateString() !== e.toDateString()) cx.push('evento-apos-meia-noite');
            }
            return cx;
        },

        datesSet(info) {
            calendarTitle.innerText = "Minha Agenda - " + info.view.title;

            // Atualiza feriados de fundo para a janela atual
            if (feriadosSource) {
                calendar.getEventSourceById('feriados-bg')?.remove();
            }
            feriadosSource = calendar.addEventSource({
                id: 'feriados-bg',
                events: buildHolidayBgEvents(info.start, info.end),
            });
        },

        // Sáb/Dom de fundo (recorrente)
        eventSources: [
            weekendBackground
        ],

        // criar por clique/arraste
        select: (sel) => {
            abrirModalCriar(sel.startStr.slice(0,16)); // 'YYYY-MM-DDTHH:mm'
            calendar.unselect();
        },
        dateClick: (info) => {
            const base = info.dateStr.length <= 10 ? info.dateStr + 'T09:00' : info.dateStr;
            abrirModalCriar(base);
        },

        // renderização textual
        eventContent: function (arg) {
            const viewType = arg.view.type;
            const event = arg.event;

            const start = event.start;
            const end = event.end;

            const formatHora = (date) =>
                date.getHours().toString().padStart(2, '0') + ':' +
                date.getMinutes().toString().padStart(2, '0');

            const horaInicio = start ? formatHora(start) : '';
            const horaFim = end ? formatHora(end) : '';
            const titulo = event.title || '';

            if (viewType === 'dayGridMonth') {
                return { html: `<div>${horaInicio} - ${titulo}</div>` };
            } else {
                return { html: `<div>${horaInicio}${horaFim ? ' - ' + horaFim : ''}</div><div>${titulo}</div>` };
            }
        },

        // abrir modal ao clicar no evento
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
                campos.foi_pago.checked = !!sessao.foi_pago;
                campos.titulo.innerText = "Editar Sessão";

                modal.show();
            } catch {
                Swal.fire('Erro', 'Erro ao carregar dados da sessão.', 'error');
            }
        },

        eventDidMount: function (info) {
            const tooltip = info.event.extendedProps?.tooltip;
            if (tooltip) info.el.setAttribute('title', tooltip);
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
            document.querySelectorAll('.calendar-controls .btn').forEach(b => b.classList.remove('active'));
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
            btnSalvar.disabled = true;
            btnSalvar.innerText = 'Salvando...';

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
                if (resData?.message?.toLowerCase().includes("sessão") || resData?.message?.toLowerCase().includes("conflito")) {
                    Swal.fire({ icon: 'error', title: 'Conflito de Horário', text: resData.message });
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
                timer: 1600,
                showConfirmButton: false
            });

        } catch (error) {
            Swal.fire('Erro', error.message || 'Erro inesperado ao salvar a sessão.', 'error');
        } finally {
            btnSalvar.disabled = false;
            btnSalvar.innerText = 'Salvar';
        }
    });
});
</script>
@endsection
