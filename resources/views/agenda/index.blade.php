@extends('layouts.app')

@section('title', 'Agenda | PsiGestor')

@section('styles')
{{-- Bootstrap Icons para os <i class="bi ..."> --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root{
        --pg-primary:#12B4B7;         /* turquesa PsiGestor */
        --pg-primary-600:#0ea2a5;
        --pg-primary-700:#0b7f83;
        --pg-ink:#15181f;
        --pg-muted:#6c7685;
        --pg-border:#e6eaef;
        --pg-surface:#ffffff;
    }

    /* ====== MOBILE FIRST ====== */

    /* Card do calendário */
    #calendar-card{
        border:1px solid var(--pg-border);
        border-radius:16px;
        background:var(--pg-surface);
        padding:14px;                 /* mobile: um pouco mais compacto */
        box-shadow:0 6px 18px rgba(10,20,30,.06);
    }

    /* Cabeçalho */
    .calendar-header{
        position:relative;
        background:var(--pg-surface);
        border:1px solid var(--pg-border);
        border-radius:16px;
        padding:14px;                 /* mobile first */
        margin-bottom:14px;
        box-shadow:0 8px 28px rgba(10,20,30,.06);
    }
    .calendar-header:before{
        content:"";
        position:absolute; inset:0 0 auto 0;
        height:4px;
        border-radius:16px 16px 0 0;
        background:linear-gradient(90deg,var(--pg-primary),#30d1d4);
    }

    /* Grid do cabeçalho: mobile em coluna */
    .calendar-row{
        display:grid;
        gap:12px;
        grid-template-columns:1fr;    /* mobile: 1 coluna */
    }

    /* Bloco do título */
    .cal-title{
        display:flex; gap:10px; align-items:center; justify-content:center; /* mobile centralizado */
        text-align:center;
    }
    .cal-icon{
        width:36px; height:36px; border-radius:10px;
        display:grid; place-items:center;
        background:rgba(18,180,183,.10);
        color:var(--pg-primary);
        font-size:18px;
        flex:0 0 auto;
    }
    .cal-text .cal-head{
        font-weight:800; letter-spacing:.2px;
        color:var(--pg-ink); line-height:1.1;
        font-size:1.125rem;           /* mobile */
    }
    .cal-text .cal-sub{
        color:var(--pg-muted); font-size:.9rem;
    }

    /* Chips/Badges (contraste reforçado) */
    .pg-chip{
        display:inline-flex; align-items:center; gap:6px;
        border:1px solid #cfe7e8;
        background:rgba(18,180,183,.10);
        color:#0b7f83;
        padding:7px 12px; border-radius:999px; font-weight:700; font-size:.85rem;
        line-height:1; text-decoration:none;
        white-space:nowrap;
    }
    .pg-chip:hover{ background:rgba(18,180,183,.14) }
    .pg-chip i{font-size:1rem}

    .chip-success{
        background:rgba(16,185,129,.14);
        border-color:rgba(16,185,129,.35);
        color:#0e7a56;
    }
    .chip-warn{
        background:rgba(234,88,12,.14);
        border-color:rgba(234,88,12,.35);
        color:#8e3a0d;
    }

    /* Área de ações: mobile com rolagem horizontal */
    .cal-actions{
        display:flex;
        gap:8px;
        flex-wrap:nowrap;             /* mobile: em linha */
        overflow-x:auto;
        -webkit-overflow-scrolling:touch;
        padding-bottom:4px;           /* espaço pro indicador de scroll */
        scrollbar-width:none;         /* Firefox: some o trilho */
        justify-content:flex-start;
    }
    .cal-actions::-webkit-scrollbar{ display:none; } /* Chrome/Safari */

    /* Grupos compactos (cada grupo vira uma "pílula" de botões) */
    .btn-group-clean{ display:flex; gap:8px; align-items:center; flex:0 0 auto; }
    .divider-dot{ display:none; } /* some no mobile */

    /* Botões */
    .btn-ghost, .btn-brand, .btn-outline{
        display:inline-flex; align-items:center; justify-content:center; gap:6px;
        border-radius:12px; font-weight:800; letter-spacing:.2px;
        padding:10px 14px; font-size:.92rem;
        min-height:44px;              /* conforto de toque */
        min-width:90px;               /* dá corpo aos botões no carrossel */
        transition:transform .15s ease, box-shadow .15s ease, background .15s ease;
        flex:0 0 auto;                /* impede encolher no carrossel */
    }
    .btn-ghost{
        background:#eef5f6; border:1px solid #cfe7e8; color:#184146;
    }
    .btn-ghost:hover{ transform:translateY(-1px); box-shadow:0 6px 14px rgba(10,20,30,.10) }

    .btn-outline{
        background:#fff; border:1px solid var(--pg-primary);
        color:var(--pg-primary);
    }
    .btn-outline:hover{ background:rgba(18,180,183,.08) }

    .btn-brand{
        background:var(--pg-primary); border:1px solid var(--pg-primary-700); color:#fff;
        box-shadow:0 8px 18px rgba(18,180,183,.28);
    }
    .btn-brand:hover{ transform:translateY(-1px); }

    /* Controles (setas e views) no carrossel */
    .view-switch .vbtn, .nav-switch .nbtn{
        display:inline-flex; align-items:center; justify-content:center;
        border:1px solid #cfe7e8; background:#fff; color:#1f2a33;
        border-radius:12px; padding:9px 12px; font-weight:800; font-size:.92rem;
        min-height:44px; min-width:90px;
        flex:0 0 auto;
    }
    .view-switch .vbtn.active{
        background:linear-gradient(180deg,#fff,#f0feff);
        border-color:var(--pg-primary);
        box-shadow:0 8px 18px rgba(18,180,183,.22);
        color:var(--pg-primary);
    }
    .nav-switch .nbtn:hover,
    .view-switch .vbtn:hover{ transform:translateY(-1px) }

    /* Legenda mini: quebra em múltiplas linhas no mobile */
    .legend{
        display:flex; flex-wrap:wrap; gap:8px; align-items:center; justify-content:center;
    }
    .legend .dot{
        width:12px; height:12px; border-radius:999px; display:inline-block; margin-right:8px;
        box-shadow:0 0 0 2px #fff, 0 0 0 3px #cfe7e8;
    }
    .dot-paid{ background:#28a745 }
    .dot-pending{ background:#dc3545 }
    .dot-late{ background:#00c4ff }

    /* ====== UPGRADES PARA TELAS MÉDIAS+ ====== */
    @media (min-width: 768px){
        #calendar-card{ padding:18px; }

        .calendar-row{
            grid-template-columns:1fr auto;   /* à esquerda título, à direita ações */
            align-items:center;
        }
        .cal-title{ justify-content:flex-start; text-align:left; }
        .cal-text .cal-head{ font-size:1.25rem; }
        .divider-dot{ display:inline-block; }

        .cal-right{ display:flex; justify-content:flex-end; }
        .cal-actions{ overflow:visible; flex-wrap:wrap; }
        .btn-ghost, .btn-brand, .btn-outline,
        .view-switch .vbtn, .nav-switch .nbtn{
            min-width:auto;                  /* em desktop, deixam de ter largura mínima */
        }
    }

    @media (min-width: 992px){
        .cal-text .cal-head{ font-size:1.35rem; }
    }

    /* FullCalendar eventos (mantido) */
    .fc-event{ border-radius:6px !important; padding:4px !important; font-size:.9rem !important; }
    .fc .fc-event.evento-pago{
        background:linear-gradient(90deg,#28a745,#218838)!important;border:none!important;color:#fff!important;font-weight:700!important;
    }
    .fc .fc-event.evento-pendente{
        background:linear-gradient(90deg,#dc3545,#a71d2a)!important;border:none!important;color:#fff!important;font-weight:700!important;
    }
    .fc .fc-event.evento-apos-meia-noite{
        background:#e3f7ff!important;border:1px dashed #00c4ff!important;color:#006f99!important;font-weight:700!important;
    }
    .fc-daygrid-event-dot{ display:none!important; }
</style>
@endsection

@section('content')
<div class="container">

    {{-- Cabeçalho moderno PsiGestor (mobile-first) --}}
    <div class="calendar-header">
        <div class="calendar-row">
            {{-- ESQUERDA (no mobile: primeira linha): Título + status + legenda --}}
            <div class="cal-left">
                <div class="cal-title">
                    <div class="cal-icon">
                        <i class="bi bi-calendar3-event"></i>
                    </div>
                    <div class="cal-text">
                        <div class="cal-head">
                            <span id="calendarTitle">Minha Agenda</span>
                        </div>
                        <div class="cal-sub d-flex flex-wrap align-items-center gap-2 mt-1 justify-content-center justify-content-md-start">
                            @if(auth()->user()?->google_connected)
                                <span class="pg-chip chip-success">
                                    <i class="bi bi-google"></i> Google Agenda conectado
                                </span>
                                <a href="{{ route('google.connect') }}" class="pg-chip">
                                    <i class="bi bi-arrow-repeat"></i> Reautenticar
                                </a>
                                <form action="{{ route('google.disconnect') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="pg-chip" title="Desconectar Google">
                                        <i class="bi bi-x-circle"></i> Desconectar
                                    </button>
                                </form>
                            @else
                                <span class="pg-chip chip-warn">
                                    <i class="bi bi-exclamation-triangle"></i> Google não conectado
                                </span>
                                <a href="{{ route('google.connect') }}" class="btn-outline">
                                    <i class="bi bi-google me-1"></i> Conectar ao Google
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Legenda compacta (central em mobile) --}}
                <div class="legend mt-3">
                    <span class="pg-chip"><span class="dot dot-paid"></span> Pago</span>
                    <span class="pg-chip"><span class="dot dot-pending"></span> Pendente</span>
                    <span class="pg-chip"><span class="dot dot-late"></span> Após 00:00</span>
                </div>
            </div>

            {{-- DIREITA (no mobile: segunda linha): Navegação + views + ações rápidas --}}
            <div class="cal-right">
                <div class="cal-actions">
                    <div class="btn-group-clean nav-switch">
                        <button id="prevBtn" class="nbtn btn-ghost" aria-label="Mês anterior">←</button>
                        <button id="todayBtn" class="nbtn btn-ghost">Hoje</button>
                        <button id="nextBtn" class="nbtn btn-ghost" aria-label="Próximo mês">→</button>
                        <span class="divider-dot d-none d-md-inline-block"></span>
                    </div>

                    <div class="btn-group-clean view-switch">
                        <button id="monthBtn" class="vbtn active">Mês</button>
                        <button id="weekBtn" class="vbtn">Semana</button>
                        <button id="dayBtn" class="vbtn">Dia</button>
                    </div>

                    <span class="divider-dot d-none d-md-inline-block"></span>

                    <div class="btn-group-clean">
                        <button class="btn-ghost" onclick="window.location.href='{{ route('sessoes.index') }}'">
                            <i class="bi bi-list-ul me-1"></i> Lista
                        </button>
                        <button class="btn-brand" data-bs-toggle="modal" data-bs-target="#modalSessao">
                            <i class="bi bi-plus-lg me-1"></i> Nova sessão
                        </button>
                    </div>
                </div>
            </div>
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
    const calendarEl   = document.getElementById('calendar');
    const calendarH1   = document.getElementById('calendarTitle');
    const modal        = new bootstrap.Modal(document.getElementById('modalSessao'));

    const campos = {
        id:        document.getElementById('sessao_id'),
        paciente:  document.getElementById('paciente_id'),
        data_hora: document.getElementById('data_hora'),
        valor:     document.getElementById('valor'),
        duracao:   document.getElementById('duracao'),
        foi_pago:  document.getElementById('foi_pago'),
        titulo:    document.getElementById('modalTitulo'),
    };

    if (!window.FullCalendar || !calendarEl) return;

    // Título com tipografia “bonita” (substitui “ de ” por “ · ”)
    const prettyTitle = (t) => t.replace(/ de /g, ' · ');

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

        // Atualiza o título estilizado e sincroniza o botão ativo
        datesSet: function(info) {
            if (calendarH1) {
                calendarH1.innerHTML = `<span>${prettyTitle(info.view.title)}</span>`;
            }
            syncActiveViewButton(info.view.type);
        },

        eventContent: function (arg) {
            const viewType = arg.view.type;
            const event    = arg.event;

            const start = event.start;
            const end   = event.end;

            const formatHora = (date) =>
                date.getHours().toString().padStart(2, '0') + ':' +
                date.getMinutes().toString().padStart(2, '0');

            const horaInicio = formatHora(start);
            const horaFim    = end ? formatHora(end) : '';
            const titulo     = event.title;

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

                campos.id.value        = sessao.id;
                campos.paciente.value  = sessao.paciente_id;
                campos.data_hora.value = sessao.data_hora;
                campos.valor.value     = sessao.valor;
                campos.duracao.value   = sessao.duracao;
                campos.foi_pago.checked= sessao.foi_pago == true;
                campos.titulo.innerText= "Editar Sessão";

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

    // ===== Navegação (setas / hoje) =====
    const prevBtn  = document.getElementById('prevBtn');
    const nextBtn  = document.getElementById('nextBtn');
    theTodayBtn    = document.getElementById('todayBtn');

    if (prevBtn)  prevBtn.onclick  = () => calendar.prev();
    if (nextBtn)  nextBtn.onclick  = () => calendar.next();
    if (theTodayBtn) theTodayBtn.onclick = () => calendar.today();

    // ===== Views (Mês / Semana / Dia) =====
    const viewButtons = {
        monthBtn: 'dayGridMonth',
        weekBtn:  'timeGridWeek',
        dayBtn:   'timeGridDay'
    };

    Object.keys(viewButtons).forEach(id => {
        const btn = document.getElementById(id);
        if (!btn) return;
        btn.addEventListener('click', () => {
            calendar.changeView(viewButtons[id]);
            syncActiveViewButton(viewButtons[id]);
        });
    });

    function syncActiveViewButton(currentView){
        document.querySelectorAll('.view-switch .vbtn').forEach(b => b.classList.remove('active'));
        let activeId = null;
        if (currentView === 'dayGridMonth') activeId = 'monthBtn';
        if (currentView === 'timeGridWeek')  activeId = 'weekBtn';
        if (currentView === 'timeGridDay')   activeId = 'dayBtn';
        if (activeId) {
            const el = document.getElementById(activeId);
            if (el) el.classList.add('active');
        }
    }

    function abrirModalCriar(data) {
        campos.id.value          = '';
        campos.paciente.selectedIndex = 0;
        campos.data_hora.value   = data.length <= 10 ? data + 'T09:00' : data;
        campos.valor.value       = '';
        campos.duracao.value     = 50;
        campos.foi_pago.checked  = false;
        campos.titulo.innerText  = "Nova Sessão";
        modal.show();
    }

    document.getElementById('formSessao').addEventListener('submit', async function (e) {
        e.preventDefault();

        const id     = campos.id.value;
        const rota   = id ? `/sessoes-json/${id}` : `/sessoes-json`;
        const metodo = id ? 'PUT' : 'POST';

        const payload = {
            paciente_id: campos.paciente.value,
            data_hora:   campos.data_hora.value,
            valor:       campos.valor.value,
            duracao:     parseInt(campos.duracao.value),
            foi_pago:    campos.foi_pago.checked ? 1 : 0,
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
