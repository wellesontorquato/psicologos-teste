@extends('layouts.app')

@section('title', 'Agenda | PsiGestor')

@section('styles')
<style>
    /* ========== GERAL ========== */
    :root {
        --ps-primary: #0ea5e9; /* Azul claro (sky-500) */
        --ps-primary-dark: #0284c7; /* Azul escuro (sky-600) */
        --ps-primary-light: #f0f9ff; /* Azul bem claro (sky-50) */
        --ps-text-primary: #0f172a; /* Texto principal (slate-900) */
        --ps-text-secondary: #64748b; /* Texto secundário (slate-500) */
        --ps-border: #e2e8f0; /* Bordas (slate-200) */
        --ps-success: #16a34a; /* Verde (green-600) */
        --ps-danger: #dc2626; /* Vermelho (red-600) */
    }

    /* ========== CABEÇALHO DA AGENDA ========== */
    .calendar-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1.5rem; /* Aumenta o espaçamento entre os elementos */
        margin-bottom: 1.5rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--ps-border);
    }

    .calendar-title {
        flex: 1 1 auto;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .calendar-title i {
        font-size: 2rem; /* Ícone maior */
        color: var(--ps-primary);
    }
    .calendar-title .main {
        font-weight: 700;
        font-size: 1.5rem; /* Título maior */
        color: var(--ps-text-primary);
        line-height: 1.2;
    }
    .calendar-title .sub {
        font-weight: 500;
        color: var(--ps-text-secondary);
        font-size: 0.9rem;
    }

    .toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem; /* Espaçamento entre grupos de botões */
        align-items: center;
    }
    .toolbar .btn {
        font-weight: 600;
        border-radius: 0.5rem !important; /* Bordas mais arredondadas */
        transition: all 0.2s ease-in-out;
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .toolbar .btn-group { box-shadow: 0 2px 8px rgba(17,24,39,.07); }
    .toolbar .btn-group .btn { box-shadow: none !important; }

    /* Botão de Ação Principal: Nova Sessão */
    .btn-gradient-primary {
        background: linear-gradient(45deg, var(--ps-primary), var(--ps-primary-dark));
        color: white;
        border: none;
        box-shadow: 0 4px 12px rgba(2, 132, 199, .35);
    }
    .btn-gradient-primary:hover {
        color: white;
        opacity: 0.9;
        box-shadow: 0 6px 16px rgba(2, 132, 199, .4);
    }

    /* Outros botões */
    .view-controls .btn.active {
        background-color: var(--ps-primary) !important;
        border-color: var(--ps-primary-dark) !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(2, 132, 199, .35);
    }
    .toolbar .btn-outline-secondary {
        border-color: var(--ps-border);
        color: var(--ps-text-secondary);
    }
    .toolbar .btn-outline-secondary:hover {
        background-color: #f8fafc; /* slate-50 */
        color: var(--ps-text-primary);
    }


    /* ========== CARTÃO DO CALENDÁRIO ========== */
    #calendar-card {
        border: 1px solid var(--ps-border);
        border-radius: 14px;
        background: #fff;
        padding: 16px;
        box-shadow: 0 6px 18px rgba(17,24,39,.06);
    }

    /* ========== ESTILOS DOS EVENTOS (SESSÕES) ========== */
    .fc .fc-event { border-radius: 8px !important; padding: 4px 6px !important; font-size: .875rem !important; border: none; }
    .fc .evento-pago { background: linear-gradient(90deg, #16a34a, #11813b) !important; color: #fff !important; }
    .fc .evento-pendente { background: linear-gradient(90deg, #ef4444, #c62828) !important; color: #fff !important; }
    .fc .evento-cancelada { background: #e2e8f0 !important; color: #475569 !important; text-decoration: line-through; }
    .fc .evento-remarcar { background: linear-gradient(90deg, #f59e0b, #d97706) !important; color: #1f2937 !important; }
    .fc .evento-apos-meia-noite { outline: 2px dashed #38bdf8 !important; outline-offset: -2px; }
    .fc-daygrid-event-dot { display: none !important; }
    .fc .fc-daygrid-day.fc-day-today { background: var(--ps-primary-light); }

    /* ========== EVENTOS DE FUNDO ========== */
    .bg-weekend { background: rgba(2,132,199,.05) !important; }
    .bg-feriado { background: linear-gradient(135deg,rgba(250,204,21,.18),rgba(250,204,21,.12)) !important; }
</style>
@endsection

@section('content')
<div class="container-fluid px-lg-4">
    {{-- CABEÇALHO --}}
    <div class="calendar-header">
        <div class="calendar-title">
            <i class="bi bi-calendar-heart"></i>
            <div>
                <div id="calendarTitle" class="main">Minha Agenda</div>
                <div class="sub">
                    @if(auth()->user()?->google_connected)
                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill">
                            <i class="bi bi-google me-1"></i> Google Agenda Conectado
                        </span>
                    @else
                        <a href="{{ route('google.connect') }}" class="text-decoration-none">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill">
                                <i class="bi bi-google me-1"></i> Conectar ao Google Agenda
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="toolbar ms-auto">
            {{-- Ação Principal --}}
            <a href="#" id="btnNovaSessao" class="btn btn-gradient-primary">
                <i class="bi bi-plus-circle-fill me-2"></i> Nova Sessão
            </a>

            {{-- Controles de Navegação e Visualização --}}
            <div class="btn-group" role="group">
                <button id="prevBtn" title="Anterior" class="btn btn-outline-secondary"><i class="bi bi-chevron-left"></i></button>
                <button id="todayBtn" class="btn btn-outline-secondary">Hoje</button>
                <button id="nextBtn" title="Próximo" class="btn btn-outline-secondary"><i class="bi bi-chevron-right"></i></button>
            </div>

            <div class="btn-group view-controls" role="group">
                <button id="monthBtn" class="btn btn-outline-secondary active">Mês</button>
                <button id="weekBtn" class="btn btn-outline-secondary">Semana</button>
                <button id="dayBtn" class="btn btn-outline-secondary">Dia</button>
            </div>
            
            {{-- Menu Dropdown de Ações Extras --}}
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('sessoes.index') }}"><i class="bi bi-list-ul me-2"></i>Ver Lista de Sessões</a></li>
                    @if(auth()->user()?->google_connected)
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('google.connect') }}"><i class="bi bi-arrow-repeat me-2"></i>Reautenticar Google</a></li>
                        <li>
                            <form action="{{ route('google.disconnect') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja desconectar do Google Agenda?')">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-x-circle me-2"></i>Desconectar Google</button>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    {{-- CALENDÁRIO --}}
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small text-muted fw-semibold">Paciente</label>
                        <select name="paciente_id" id="paciente_id" class="form-select" required>
                            @foreach(\App\Models\Paciente::where('user_id', auth()->id())->get() as $paciente)
                                <option value="{{ $paciente->id }}">{{ $paciente->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted fw-semibold">Data e Hora</label>
                        <input type="datetime-local" name="data_hora" id="data_hora" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted fw-semibold">Valor (R$)</label>
                        <input type="number" step="0.01" name="valor" id="valor" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted fw-semibold">Duração (min)</label>
                        <input type="number" name="duracao" id="duracao" class="form-control" value="50" required>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" role="switch" name="foi_pago" id="foi_pago">
                           <label class="form-check-label small fw-semibold" for="foi_pago">Sessão Paga</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" id="btnSalvarSessao" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
{{-- Dependências --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales-all.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ================== VARIÁVEIS E CONSTANTES ==================
    const calendarEl = document.getElementById('calendar');
    const calendarTitle = document.getElementById('calendarTitle');
    const modalSessaoEl = document.getElementById('modalSessao');
    const modal = new bootstrap.Modal(modalSessaoEl);
    const btnSalvar = document.getElementById('btnSalvarSessao');

    // Mapeamento dos campos do formulário do modal
    const campos = {
        id: document.getElementById('sessao_id'),
        paciente: document.getElementById('paciente_id'),
        data_hora: document.getElementById('data_hora'),
        valor: document.getElementById('valor'),
        duracao: document.getElementById('duracao'),
        foi_pago: document.getElementById('foi_pago'),
        titulo: document.getElementById('modalTitulo'),
    };

    /**
     * Tenta obter a instância do spinner global do seu layouts.app.
     * Use `window.instance` se for uma variável global.
     * Use `window.hideSpinner` se for uma função global.
     */
    const spinnerInstance = window.instance;
    const hideSpinnerFunc = window.hideSpinner;


    // ================== FUNÇÕES AUXILIARES (FERIADOS) ==================
    function easterDate(year){const a=year%19,b=Math.floor(year/100),c=year%100,d=Math.floor(b/4),e=b%4,f=Math.floor((b+8)/25),g=Math.floor((b-f+1)/3),h=(19*a+b-d-g+15)%30,i=Math.floor(c/4),k=c%4,l=(32+2*e+2*i-h-k)%7,m=Math.floor((a+11*h+22*l)/451),month=Math.floor((h+l-7*m+114)/31),day=((h+l-7*m+114)%31)+1;return new Date(Date.UTC(year,month-1,day))}
    const toISO=d=>`${d.getUTCFullYear()}-${String(d.getUTCMonth()+1).padStart(2,'0')}-${String(d.getUTCDate()).padStart(2,'0')}`;
    const addDays=(d,n)=>{const nd=new Date(d);nd.setUTCDate(nd.getUTCDate()+n);return nd;}
    function feriadosBR(year){const p=easterDate(year);const datas=[`${year}-01-01`,`${year}-04-21`,`${year}-05-01`,`${year}-09-07`,`${year}-10-12`,`${year}-11-02`,`${year}-11-15`,`${year}-12-25`,toISO(addDays(p,-47)),toISO(addDays(p,-2)),toISO(p),toISO(addDays(p,60))];return datas;}
    let feriadosSource=null;
    function buildFeriados(start,end){const years=new Set();for(let y=start.getFullYear();y<=end.getFullYear();y++)years.add(y);const items=[];years.forEach(y=>feriadosBR(y).forEach(d=>items.push({start:d,end:d,allDay:true,display:'background',className:'bg-feriado'})));return items;}


    // ================== INICIALIZAÇÃO DO FULLCALENDAR ==================
    const calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [FullCalendar.dayGridPlugin, FullCalendar.timeGridPlugin, FullCalendar.interactionPlugin],
        locale: 'pt-br',
        timeZone: 'local',
        height: 'auto',
        headerToolbar: false, // O cabeçalho é customizado em HTML/CSS
        initialView: 'dayGridMonth',
        events: '/api/sessoes',
        selectable: true,
        selectMirror: true,
        expandRows: true,
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00',

        // Adiciona classes CSS customizadas para cada tipo de evento
        eventClassNames: (info) => {
            const x = info.event.extendedProps || {};
            const pago = (x.foi_pago === true || x.foi_pago === 1 || x.foi_pago === '1');
            const classes = [];
            if (x.status_confirmacao === 'CANCELADA') classes.push('evento-cancelada');
            else if (x.status_confirmacao === 'REMARCAR') classes.push('evento-remarcar');
            else if (pago) classes.push('evento-pago');
            else classes.push('evento-pendente');
            if (info.event.start && info.event.end && info.event.start.toDateString() !== info.event.end.toDateString()) classes.push('evento-apos-meia-noite');
            return classes;
        },

        // Atualiza o título e os feriados ao navegar
        datesSet: (info) => {
            calendarTitle.innerText = info.view.title;
            if (feriadosSource) calendar.getEventSourceById('feriados-bg')?.remove();
            feriadosSource = calendar.addEventSource({ id: 'feriados-bg', events: buildFeriados(info.start, info.end) });
        },
        
        // Fontes de eventos de fundo (fins de semana)
        eventSources: [{ events: [{ daysOfWeek: [0, 6], display: 'background', className: 'bg-weekend' }] }],

        // Ações de clique e seleção
        select: (sel) => { abrirModalCriar(sel.startStr.slice(0, 16)); calendar.unselect(); },
        dateClick: (info) => { abrirModalCriar((info.dateStr.length <= 10) ? `${info.dateStr}T09:00` : info.dateStr); },
        eventClick: async (info) => {
            info.jsEvent.preventDefault();
            try {
                const res = await fetch(`/sessoes-json/${info.event.id}`);
                if (!res.ok) throw new Error('Falha ao buscar dados da sessão.');
                const s = await res.json();
                campos.id.value = s.id;
                campos.paciente.value = s.paciente_id;
                campos.data_hora.value = s.data_hora;
                campos.valor.value = s.valor;
                campos.duracao.value = s.duracao;
                campos.foi_pago.checked = !!s.foi_pago;
                campos.titulo.innerText = "Editar Sessão";
                modal.show();
            } catch (err) {
                Swal.fire('Erro', err.message, 'error');
            }
        },
    });

    calendar.render();


    // ================== CONTROLES DO CABEÇALHO ==================
    // Navegação (Anterior, Hoje, Próximo)
    document.getElementById('prevBtn').onclick = () => calendar.prev();
    document.getElementById('nextBtn').onclick = () => calendar.next();
    document.getElementById('todayBtn').onclick = () => calendar.today();
    
    // Troca de Visualização (Mês, Semana, Dia)
    const viewBtns = { monthBtn: 'dayGridMonth', weekBtn: 'timeGridWeek', dayBtn: 'timeGridDay' };
    Object.keys(viewBtns).forEach(id => {
        const btn = document.getElementById(id);
        btn.addEventListener('click', () => {
            calendar.changeView(viewBtns[id]);
            document.querySelectorAll('.view-controls .btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });


    // ================== LÓGICA DO MODAL ==================
    function abrirModalCriar(dateStr = null) {
        document.getElementById('formSessao').reset(); // Limpa o formulário
        campos.id.value = '';
        campos.paciente.selectedIndex = 0;
        campos.data_hora.value = dateStr || new Date().toISOString().slice(0, 16);
        campos.duracao.value = 50;
        campos.titulo.innerText = "Nova Sessão";
        
        // AJUSTE DO SPINNER: Mostra o spinner ao abrir o modal de criação
        if (spinnerInstance && typeof spinnerInstance.show === 'function') {
            spinnerInstance.show();
        }
        
        modal.show();
    }
    
    // Gatilho para o botão "Nova Sessão" no cabeçalho
    document.getElementById('btnNovaSessao').addEventListener('click', (e) => {
        e.preventDefault();
        abrirModalCriar();
    });

    // AJUSTE DO SPINNER: Esconde o spinner DEPOIS que a animação do modal terminar
    modalSessaoEl.addEventListener('shown.bs.modal', () => {
        if (typeof hideSpinnerFunc === 'function') {
            hideSpinnerFunc();
        } else if (spinnerInstance && typeof spinnerInstance.hide === 'function') {
            spinnerInstance.hide(); // Fallback caso hideSpinner não exista
        }
    });

    // Submissão do formulário (Criar/Editar Sessão)
    document.getElementById('formSessao').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = campos.id.value;
        const rota = id ? `/sessoes-json/${id}` : `/sessoes-json`;
        const metodo = id ? 'PUT' : 'POST';
        const payload = {
            paciente_id: campos.paciente.value,
            data_hora: campos.data_hora.value,
            valor: campos.valor.value,
            duracao: parseInt(campos.duracao.value || '0', 10),
            foi_pago: campos.foi_pago.checked ? 1 : 0,
        };

        btnSalvar.disabled = true;
        btnSalvar.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...`;

        try {
            const res = await fetch(rota, {
                method: metodo,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (!res.ok) {
                // Se der erro, mostra a mensagem do backend ou uma genérica
                Swal.fire('Erro', data?.message || 'Não foi possível salvar a sessão.', 'error');
                return; // Importante: sai da função aqui para não fechar o modal
            }

            // AJUSTE DO SPINNER: Garante que o spinner não apareça ao fechar o modal
            // Se houver uma função global `hideSpinner`, ela será usada.
            if (typeof hideSpinnerFunc === 'function') {
                hideSpinnerFunc();
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

        } catch (err) {
            Swal.fire('Erro Inesperado', 'Ocorreu uma falha na comunicação. Tente novamente.', 'error');
        } finally {
            // Garante que o botão de salvar seja reativado em qualquer cenário (sucesso ou erro)
            btnSalvar.disabled = false;
            btnSalvar.innerText = 'Salvar';
        }
    });
});
</script>
@endsection