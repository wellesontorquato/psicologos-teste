@extends('layouts.app')

@section('title', 'Agenda | PsiGestor')

@section('styles')
<style>
    /* ---------- Cartão do calendário ---------- */
    #calendar-card{
        border:1px solid #e9ecef;border-radius:14px;background:#fff;padding:16px;
        box-shadow:0 6px 18px rgba(17,24,39,.06)
    }

    /* ---------- Toolbar limpa ---------- */
    .calendar-header{
        display:flex;flex-wrap:wrap;gap:12px;margin-bottom:16px;padding:12px;
        background:#fff;border-radius:14px;box-shadow:0 4px 14px rgba(17,24,39,.05)
    }
    .calendar-title{
        flex:1 1 260px;display:flex;align-items:center;gap:10px;min-height:42px
    }
    .calendar-title i{font-size:1.6rem;color:#0ea5e9}
    .calendar-title .main{font-weight:700;color:#0f172a;font-size:1.2rem}
    .calendar-title .sub{font-weight:600;color:#64748b;font-size:.95rem}

    .toolbar{
        flex:2 1 420px;display:flex;flex-wrap:wrap;gap:8px;justify-content:flex-end
    }
    .btn-group>.btn{border-radius:8px !important}
    .toolbar .btn{min-height:40px;font-weight:600}
    .toolbar .btn-outline-primary{border-color:#cfe8ff}
    .toolbar .btn-outline-primary:hover{background:#e7f3ff}

    /* ---------- Controles view ---------- */
    .view-controls{display:flex;gap:8px;flex-wrap:wrap}
    .view-controls .btn.active{
        background:#0ea5e9 !important;color:#fff !important;border-color:#0284c7 !important;
        box-shadow:0 4px 12px rgba(2,132,199,.35)
    }

    /* ---------- Evento (sessão) ---------- */
    .fc .fc-event{border-radius:10px !important;padding:4px 6px !important;font-size:.9rem !important;border:none}
    .fc .evento-pago{background:linear-gradient(90deg,#16a34a,#11813b) !important;color:#fff !important}
    .fc .evento-pendente{background:linear-gradient(90deg,#ef4444,#c62828) !important;color:#fff !important}
    .fc .evento-cancelada{background:#e2e8f0 !important;color:#475569 !important;text-decoration:line-through}
    .fc .evento-remarcar{background:linear-gradient(90deg,#f59e0b,#d97706) !important;color:#1f2937 !important}
    .fc .evento-apos-meia-noite{outline:2px dashed #38bdf8 !important;outline-offset:-2px}

    .fc-daygrid-event-dot{display:none !important}
    .fc .fc-daygrid-day.fc-day-today{background:#fffbe6}

    /* ---------- Eventos de fundo ---------- */
    .bg-weekend{background:rgba(2,132,199,.05) !important}
    .bg-feriado{
        background:linear-gradient(135deg,rgba(250,204,21,.18),rgba(250,204,21,.12)) !important
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="calendar-header">
        <div class="calendar-title">
            <i class="bi bi-calendar3"></i>
            <div>
                <div id="calendarTitle" class="main">Minha Agenda</div>
                <div class="sub">
                    @if(auth()->user()?->google_connected)
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            <i class="bi bi-google me-1"></i> Google Agenda conectado
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                            <i class="bi bi-google me-1"></i> Não conectado
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="toolbar">
            <div class="btn-group me-auto" role="group" aria-label="Navegação">
                <button id="prevBtn" class="btn btn-outline-primary"><i class="bi bi-chevron-left"></i></button>
                <button id="todayBtn" class="btn btn-outline-secondary">Hoje</button>
                <button id="nextBtn" class="btn btn-outline-primary"><i class="bi bi-chevron-right"></i></button>
            </div>

            <div class="btn-group view-controls" role="group" aria-label="Visualização">
                <button id="monthBtn" class="btn btn-primary active">Mês</button>
                <button id="weekBtn" class="btn btn-outline-primary">Semana</button>
                <button id="dayBtn" class="btn btn-outline-primary">Dia</button>
            </div>

            <div class="btn-group ms-auto" role="group" aria-label="Ações">
                @if(auth()->user()?->google_connected)
                    <a href="{{ route('google.connect') }}" class="btn btn-outline-success">
                        <i class="bi bi-arrow-repeat me-1"></i> Reautenticar
                    </a>
                    <form action="{{ route('google.disconnect') }}" method="POST" class="m-0"
                          onsubmit="return confirm('Desconectar do Google Agenda?')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle me-1"></i> Desconectar
                        </button>
                    </form>
                @else
                    <a href="{{ route('google.connect') }}" class="btn btn-primary">
                        <i class="bi bi-google me-1"></i> Conectar
                    </a>
                @endif
                <a href="{{ route('sessoes.create') }}" class="btn btn-outline-primary">
                    <i class="bi bi-plus-circle me-1"></i> Nova Sessão
                </a>
                <a href="{{ route('sessoes.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-list-ul me-1"></i> Ver lista
                </a>
            </div>
        </div>
    </div>

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
                <select name="paciente_id" id="paciente_id" class="form-select form-select-sm" required>
                    @foreach(\App\Models\Paciente::where('user_id', auth()->id())->get() as $paciente)
                        <option value="{{ $paciente->id }}">{{ $paciente->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small text-muted fw-semibold">Data e Hora</label>
                <input type="datetime-local" name="data_hora" id="data_hora" class="form-control form-control-sm" required>
            </div>
            <div class="col-6">
                <label class="form-label small text-muted fw-semibold">Valor (R$)</label>
                <input type="number" step="0.01" name="valor" id="valor" class="form-control form-control-sm" required>
            </div>
            <div class="col-6">
                <label class="form-label small text-muted fw-semibold">Duração (min)</label>
                <input type="number" name="duracao" id="duracao" class="form-control form-control-sm" value="50" required>
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const calendarEl   = document.getElementById('calendar');
    const calendarTitle= document.getElementById('calendarTitle');
    const modal        = new bootstrap.Modal(document.getElementById('modalSessao'));
    const btnSalvar    = document.getElementById('btnSalvarSessao');

    const campos = {
        id: document.getElementById('sessao_id'),
        paciente: document.getElementById('paciente_id'),
        data_hora: document.getElementById('data_hora'),
        valor: document.getElementById('valor'),
        duracao: document.getElementById('duracao'),
        foi_pago: document.getElementById('foi_pago'),
        titulo: document.getElementById('modalTitulo'),
    };

    /* ===== feriados BR ===== */
    function easterDate(year){const a=year%19,b=Math.floor(year/100),c=year%100,d=Math.floor(b/4),e=b%4,f=Math.floor((b+8)/25),g=Math.floor((b-f+1)/3),h=(19*a+b-d-g+15)%30,i=Math.floor(c/4),k=c%4,l=(32+2*e+2*i-h-k)%7,m=Math.floor((a+11*h+22*l)/451),month=Math.floor((h+l-7*m+114)/31),day=((h+l-7*m+114)%31)+1;return new Date(Date.UTC(year,month-1,day))}
    const toISO = d => `${d.getUTCFullYear()}-${String(d.getUTCMonth()+1).padStart(2,'0')}-${String(d.getUTCDate()).padStart(2,'0')}`;
    const addDays = (d,n)=>{const nd=new Date(d);nd.setUTCDate(nd.getUTCDate()+n);return nd;}
    function feriadosBR(year){
        const p = easterDate(year);
        const datas = [
            `${year}-01-01`, `${year}-04-21`, `${year}-05-01`, `${year}-09-07`,
            `${year}-10-12`, `${year}-11-02`, `${year}-11-15`, `${year}-12-25`,
            toISO(addDays(p,-47)), toISO(addDays(p,-2)), toISO(p), toISO(addDays(p,60))
        ];
        return datas;
    }
    const weekendBackground = { events:[{daysOfWeek:[0,6],startTime:'00:00',endTime:'24:00',display:'background',className:'bg-weekend'}] };
    let feriadosSource=null;
    function buildFeriados(start,end){
        const years=new Set();for(let y=start.getFullYear();y<=end.getFullYear();y++)years.add(y);
        const items=[];years.forEach(y=>feriadosBR(y).forEach(d=>items.push({
            start:d,end:d,allDay:true,display:'background',className:'bg-feriado'
        })));
        return items;
    }

    const calendar = new FullCalendar.Calendar(calendarEl,{
        plugins:[ FullCalendar.dayGridPlugin, FullCalendar.timeGridPlugin, FullCalendar.interactionPlugin, FullCalendar.bootstrap5Plugin ],
        themeSystem:'bootstrap5',
        locale:'pt-br',
        timeZone:'local',
        height:650,
        headerToolbar:false,
        initialView:'dayGridMonth',
        events:'/api/sessoes',
        selectable:true, selectMirror:true, expandRows:true,
        eventTimeFormat:{hour:'2-digit',minute:'2-digit',hour12:false},
        slotMinTime:'07:00:00', slotMaxTime:'22:00:00',

        eventClassNames(info){
            const x = info.event.extendedProps || {};
            // normaliza boolean do back-end
            const pago = (x.foi_pago === true || x.foi_pago === 1 || x.foi_pago === '1');
            const pend = (x.foi_pago === false || x.foi_pago === 0 || x.foi_pago === '0');

            const classes=[];
            if (x.status_confirmacao === 'CANCELADA') classes.push('evento-cancelada');
            else if (x.status_confirmacao === 'REMARCAR') classes.push('evento-remarcar');
            else if (pago) classes.push('evento-pago');
            else if (pend) classes.push('evento-pendente');

            if (info.event.start && info.event.end && info.event.start.toDateString()!==info.event.end.toDateString())
                classes.push('evento-apos-meia-noite');

            return classes;
        },

        datesSet(info){
            calendarTitle.innerText = `Minha Agenda - ${info.view.title}`;
            if (feriadosSource) calendar.getEventSourceById('feriados-bg')?.remove();
            feriadosSource = calendar.addEventSource({ id:'feriados-bg', events: buildFeriados(info.start, info.end) });
        },

        eventSources:[ weekendBackground ],

        select(sel){ abrirModalCriar(sel.startStr.slice(0,16)); calendar.unselect(); },
        dateClick(info){ abrirModalCriar((info.dateStr.length<=10)? `${info.dateStr}T09:00` : info.dateStr); },

        eventContent(arg){
            const s=arg.event.start, e=arg.event.end, t=arg.event.title||'';
            const hhmm = d => d ? String(d.getHours()).padStart(2,'0')+':'+String(d.getMinutes()).padStart(2,'0') : '';
            if (arg.view.type==='dayGridMonth') {
                return { html:`<div>${hhmm(s)} - ${t}</div>` };
            }
            return { html:`<div>${hhmm(s)}${e?' - '+hhmm(e):''}</div><div>${t}</div>` };
        },

        eventClick: async (info)=>{
            info.jsEvent.preventDefault();
            try{
                const res = await fetch(`/sessoes-json/${info.event.id}`);
                if(!res.ok) throw 0;
                const s = await res.json();
                campos.id.value = s.id; campos.paciente.value = s.paciente_id;
                campos.data_hora.value = s.data_hora; campos.valor.value = s.valor;
                campos.duracao.value = s.duracao; campos.foi_pago.checked = !!s.foi_pago;
                campos.titulo.innerText = "Editar Sessão";
                modal.show();
            }catch{ Swal.fire('Erro','Erro ao carregar dados da sessão.','error'); }
        },
    });

    calendar.render();

    // Navegação
    document.getElementById('prevBtn').onclick = ()=>calendar.prev();
    document.getElementById('nextBtn').onclick = ()=>calendar.next();
    document.getElementById('todayBtn').onclick= ()=>calendar.today();

    // Views
    const viewBtns={monthBtn:'dayGridMonth',weekBtn:'timeGridWeek',dayBtn:'timeGridDay'};
    Object.keys(viewBtns).forEach(id=>{
        const btn=document.getElementById(id);
        btn.addEventListener('click',()=>{
            calendar.changeView(viewBtns[id]);
            document.querySelectorAll('.view-controls .btn').forEach(b=>b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    function abrirModalCriar(dateStr){
        campos.id.value=''; campos.paciente.selectedIndex=0;
        campos.data_hora.value = dateStr; campos.valor.value='';
        campos.duracao.value=50; campos.foi_pago.checked=false;
        campos.titulo.innerText="Nova Sessão";
        modal.show();
    }

    document.getElementById('formSessao').addEventListener('submit', async (e)=>{
        e.preventDefault();
        const id = campos.id.value;
        const rota = id? `/sessoes-json/${id}` : `/sessoes-json`;
        const metodo = id? 'PUT' : 'POST';
        const payload = {
            paciente_id: campos.paciente.value,
            data_hora: campos.data_hora.value,
            valor: campos.valor.value,
            duracao: parseInt(campos.duracao.value||'0',10),
            foi_pago: campos.foi_pago.checked ? 1 : 0,
        };

        try{
            btnSalvar.disabled=true; btnSalvar.innerText='Salvando...';
            const r = await fetch(rota,{method:metodo, headers:{
                'X-CSRF-TOKEN':document.querySelector('input[name="_token"]').value,
                'Accept':'application/json','Content-Type':'application/json'
            }, body:JSON.stringify(payload)});
            const data = await r.json();
            if(!r.ok){
                Swal.fire('Erro', data?.message || 'Erro ao salvar a sessão.','error');
                return;
            }
            modal.hide(); calendar.refetchEvents();
            Swal.fire({icon:'success',title:'Sucesso!',text:id?'Sessão atualizada!':'Sessão criada!',timer:1500,showConfirmButton:false});
        }catch(err){
            Swal.fire('Erro', err?.message || 'Falha inesperada.','error');
        }finally{
            btnSalvar.disabled=false; btnSalvar.innerText='Salvar';
        }
    });
});
</script>
@endsection
