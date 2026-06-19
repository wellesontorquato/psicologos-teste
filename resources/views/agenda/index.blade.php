@extends('layouts.app')

@section('title', 'Agenda | PsiGestor')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />

<style>
    :root {
        --pg-primary: #12B4B7;
        --pg-primary-600: #0ea2a5;
        --pg-primary-700: #0b7f83;
        --pg-ink: #0f172a;
        --pg-muted: #64748b;
        --pg-border: #e2e8f0;
        --pg-surface: #ffffff;
        --pg-weekend: #f8fafc;
        --pg-holiday: #eff6ff;
        --pg-holiday-border: #bfdbfe;
    }

    /* Layout Base */
    .agnd-page {
        width: 100%;
    }

    .agnd-content {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Cabeçalho Premium */
    .agnd-header-card {
        background: rgba(255,255,255,.98);
        border: 1px solid rgba(226,232,240,.95);
        border-radius: 22px;
        box-shadow: 0 10px 30px rgba(15,23,42,.04);
        padding: 24px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .agnd-header-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--pg-primary), #30d1d4);
    }

    .agnd-header-top {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 20px;
    }

    .agnd-title-group {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .agnd-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(18,180,183,.10);
        color: var(--pg-primary);
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .agnd-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--pg-ink);
        margin: 0 0 6px 0;
        line-height: 1.2;
    }

    /* Chips e Google Sync */
    .agnd-sync-area {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .agnd-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .agnd-badge-success { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
    .agnd-badge-warning { background: #fef3c7; color: #92400e; border-color: #fde68a; }
    .agnd-badge-outline { background: #f8fafc; color: #475569; border-color: #e2e8f0; cursor: pointer; }
    .agnd-badge-outline:hover { background: #f1f5f9; color: #0f172a; }

    .agnd-main-actions {
        display: flex;
        gap: 10px;
        width: 100%;
    }

    /* Botões Gerais */
    .agnd-btn {
        min-height: 44px;
        border-radius: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 18px;
        font-size: .9rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        width: 100%;
    }

    .agnd-btn-primary { background: var(--pg-primary); color: #fff; box-shadow: 0 4px 12px rgba(18,180,183,.25); }
    .agnd-btn-primary:hover { background: var(--pg-primary-700); transform: translateY(-1px); }
    .agnd-btn-outline { background: #fff; border: 2px solid #e2e8f0; color: #475569; }
    .agnd-btn-outline:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }

    /* Controles do Calendário (Mês, Semana, Dia) */
    .agnd-header-bottom {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px solid var(--pg-border);
    }

    .agnd-nav-group {
        display: flex;
        background: #f1f5f9;
        border-radius: 12px;
        padding: 4px;
        width: 100%;
    }

    .agnd-nav-btn {
        flex: 1;
        background: transparent;
        border: none;
        border-radius: 8px;
        color: #64748b;
        font-weight: 700;
        font-size: .85rem;
        padding: 8px 12px;
        transition: all 0.2s;
    }

    .agnd-nav-btn:hover { color: #0f172a; }
    .agnd-nav-btn.active { background: #fff; color: var(--pg-primary); box-shadow: 0 2px 8px rgba(0,0,0,.05); }

    /* Container do FullCalendar */
    #calendar-card {
        background: rgba(255,255,255,.98);
        border: 1px solid var(--pg-border);
        border-radius: 22px;
        box-shadow: 0 10px 30px rgba(15,23,42,.04);
        padding: 16px;
        overflow: hidden;
    }

    /* Legenda */
    .agnd-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: center;
        margin-top: 20px;
        background: #fff;
        padding: 12px 20px;
        border-radius: 16px;
        border: 1px solid var(--pg-border);
        box-shadow: 0 4px 12px rgba(15,23,42,.02);
    }

    .agnd-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .85rem;
        font-weight: 700;
        color: #475569;
    }

    .agnd-legend-dot {
        width: 12px; height: 12px; border-radius: 50%;
    }

    /* Customizações do FullCalendar */
    .fc { font-family: inherit; }
    .fc .fc-day-sat, .fc .fc-day-sun { background: var(--pg-weekend); }
    .fc .pg-feriado { background: var(--pg-holiday) !important; box-shadow: inset 0 0 0 1px var(--pg-holiday-border); }
    .fc-daygrid-day.pg-feriado .fc-daygrid-day-number::after {
        content: "Feriado"; display: inline-block; margin-left: 6px; padding: 2px 8px;
        font-size: .65rem; font-weight: 800; color: #1e3a8a; background: #dbeafe;
        border: 1px solid var(--pg-holiday-border); border-radius: 999px; vertical-align: middle;
    }
    .fc-event { border-radius: 6px !important; padding: 4px 6px !important; font-size: .85rem !important; border: none !important; }
    .fc .fc-event.evento-pago { background: #22c55e !important; color: #fff !important; font-weight: 700 !important; }
    .fc .fc-event.evento-pendente { background: #ef4444 !important; color: #fff !important; font-weight: 700 !important; }
    .fc .fc-event.evento-apos-meia-noite { background: #f0f9ff !important; border: 1px dashed #3b82f6 !important; color: #1d4ed8 !important; font-weight: 700 !important; }
    .fc-daygrid-event-dot { display: none !important; }

    /* Modal Form Fields */
    .agnd-field label { font-size: .86rem; font-weight: 700; color: #334155; margin-bottom: 6px; }
    .agnd-field .form-control, .agnd-field .form-select {
        min-height: 48px; border-radius: 12px; border-color: #e2e8f0; font-size: .95rem; background: #f8fafc;
    }
    .agnd-field .form-control:focus, .agnd-field .form-select:focus {
        border-color: var(--pg-primary); background: #fff; box-shadow: 0 0 0 .25rem rgba(18,180,183,.15);
    }

    @media (min-width: 768px) {
        .agnd-header-top { flex-direction: row; justify-content: space-between; align-items: flex-start; }
        .agnd-main-actions { width: auto; }
        .agnd-btn { width: auto; }
        .agnd-header-bottom { flex-direction: row; justify-content: space-between; align-items: center; }
        .agnd-nav-group { width: auto; }
        #calendar-card { padding: 24px; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-2 agnd-page">
    <div class="agnd-content">
        
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-4" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="agnd-header-card">
            <div class="agnd-header-top">
                <div class="agnd-title-group">
                    <div class="agnd-icon"><i class="bi bi-calendar3-event"></i></div>
                    <div>
                        <h1 class="agnd-title" id="calendarTitle">Minha Agenda</h1>
                        
                        <div class="agnd-sync-area">
                            @if(auth()->user()?->google_connected)
                                <span class="agnd-badge agnd-badge-success">
                                    <i class="bi bi-google"></i> Conectado
                                </span>
                                <form action="{{ route('google.disconnect') }}" method="POST" class="d-inline m-0 p-0">
                                    @csrf
                                    <button type="submit" class="agnd-badge agnd-badge-outline" title="Desconectar">
                                        Desconectar
                                    </button>
                                </form>
                                <form action="{{ route('sessoes.sync.futuras') }}" method="POST" class="d-inline m-0 p-0">
                                    @csrf
                                    <button type="submit" class="agnd-badge agnd-badge-outline">
                                        <i class="bi bi-arrow-repeat"></i> Sync Futuras
                                    </button>
                                </form>
                                <form action="{{ route('sessoes.sync.todas') }}" method="POST" class="d-inline m-0 p-0">
                                    @csrf
                                    <button type="submit" class="agnd-badge agnd-badge-outline">
                                        <i class="bi bi-cloud-arrow-up"></i> Sync Todas
                                    </button>
                                </form>
                            @else
                                <span class="agnd-badge agnd-badge-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Google não conectado
                                </span>
                                <a href="{{ route('google.connect') }}" class="agnd-badge agnd-badge-outline">
                                    <i class="bi bi-google"></i> Conectar
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="agnd-main-actions">
                    <button class="agnd-btn agnd-btn-outline" onclick="window.location.href='{{ route('sessoes.index') }}'">
                        <i class="bi bi-list-ul"></i> Lista
                    </button>
                    <button class="agnd-btn agnd-btn-primary" data-bs-toggle="modal" data-bs-target="#modalSessao">
                        <i class="bi bi-plus-lg"></i> Nova Sessão
                    </button>
                </div>
            </div>

            <div class="agnd-header-bottom">
                <div class="agnd-nav-group">
                    <button id="prevBtn" class="agnd-nav-btn" aria-label="Anterior"><i class="bi bi-chevron-left"></i></button>
                    <button id="todayBtn" class="agnd-nav-btn">Hoje</button>
                    <button id="nextBtn" class="agnd-nav-btn" aria-label="Próximo"><i class="bi bi-chevron-right"></i></button>
                </div>

                <div class="agnd-nav-group view-switch">
                    <button id="monthBtn" class="agnd-nav-btn vbtn active">Mês</button>
                    <button id="weekBtn" class="agnd-nav-btn vbtn">Semana</button>
                    <button id="dayBtn" class="agnd-nav-btn vbtn">Dia</button>
                </div>
            </div>
        </div>

        <div id="calendar-card">
            <div id="calendar"></div>
        </div>

        <div class="agnd-legend">
            <div class="agnd-legend-item">
                <div class="agnd-legend-dot" style="background: #22c55e;"></div> Pago
            </div>
            <div class="agnd-legend-item">
                <div class="agnd-legend-dot" style="background: #ef4444;"></div> Pendente
            </div>
            <div class="agnd-legend-item text-primary">
                <i class="bi bi-moon-stars-fill"></i> Após 00:00
            </div>
        </div>
    </div>
</div>

<!-- Container do Popover (JS injeta o HTML interno) -->
<div id="session-popover" class="session-popover" style="display:none;" role="dialog" aria-modal="true" aria-live="polite"></div>

<!-- Modal Nova Sessão -->
<div class="modal fade" id="modalSessao" tabindex="-1" aria-labelledby="modalSessaoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formSessao" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <input type="hidden" name="id" id="sessao_id">
            
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold fs-5 text-dark" id="modalTitulo">Nova Sessão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12 agnd-field">
                        <label for="paciente_id">Paciente</label>
                        <select name="paciente_id" id="paciente_id" class="form-select" required>
                            @foreach(\App\Models\Paciente::where('user_id', auth()->id())->get() as $paciente)
                                <option value="{{ $paciente->id }}">{{ $paciente->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-12 agnd-field">
                        <label for="data_hora">Data e Hora</label>
                        <input type="datetime-local" name="data_hora" id="data_hora" class="form-control" required>
                    </div>
                    
                    <div class="col-6 agnd-field">
                        <label for="valor">Valor (R$)</label>
                        <input type="number" step="0.01" name="valor" id="valor" class="form-control" placeholder="0,00" required>
                    </div>
                    
                    <div class="col-6 agnd-field">
                        <label for="duracao">Duração (min)</label>
                        <input type="number" name="duracao" id="duracao" class="form-control" value="50" required>
                    </div>
                    
                    <div class="col-12 mt-3">
                        <div class="form-check d-flex align-items-center gap-2 bg-light p-3 rounded-3 border">
                            <input type="checkbox" name="foi_pago" id="foi_pago" class="form-check-input m-0" style="width: 20px; height: 20px; cursor: pointer;">
                            <label class="form-check-label fw-bold text-dark m-0" for="foi_pago" style="cursor: pointer;">O paciente já realizou o pagamento</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-top-0 pt-0 d-flex flex-column flex-md-row gap-2">
                <button type="button" class="agnd-btn agnd-btn-outline w-100 w-md-auto m-0" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="agnd-btn agnd-btn-primary w-100 w-md-auto m-0">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', async function () {
  const calendarEl = document.getElementById('calendar');
  const calendarH1 = document.getElementById('calendarTitle');
  const modal      = new bootstrap.Modal(document.getElementById('modalSessao'));

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

  function promptReconnectIfNeeded(message, status){
    const needs = /Reconecte sua conta do Google|conexão com o Google expirou|tokens? do Google inválidos/i.test(message || '')
               || (status === 401 || status === 403);
    if (!needs) return false;
    Swal.fire({
      icon: 'warning',
      title: 'Conexão com o Google expirada',
      text: 'Reconecte sua conta para voltar a usar o Google Agenda.',
      confirmButtonText: 'Reconectar agora',
      showCancelButton: true
    }).then(r => { if (r.isConfirmed) window.location.href = "{{ route('google.connect') }}"; });
    return true;
  }

  let sessionPopover = document.getElementById('session-popover');
  if (!sessionPopover) {
    sessionPopover = document.createElement('div');
    sessionPopover.id = 'session-popover';
    sessionPopover.className = 'session-popover';
    sessionPopover.style.cssText = 'display:none;position:absolute;z-index:1060;width:320px;max-width:calc(100vw - 24px);background:#fff;border:1px solid #e2e8f0;border-radius:18px;box-shadow:0 14px 38px rgba(15,23,42,.12);overflow:hidden';
    sessionPopover.setAttribute('role','dialog');
    sessionPopover.setAttribute('aria-modal','true');
    sessionPopover.setAttribute('aria-live','polite');
    document.body.appendChild(sessionPopover);
  }

  let hideTimer = null;

  function closeSessionPopover(){
    if (sessionPopover.style.display === 'none') return;
    sessionPopover.classList.remove('show');
    sessionPopover.classList.add('hiding');

    const finish = () => {
      sessionPopover.style.display = 'none';
      sessionPopover.classList.remove('hiding');
      sessionPopover.innerHTML = '';
    };

    sessionPopover.addEventListener('transitionend', finish, { once:true });
    clearTimeout(hideTimer);
    hideTimer = setTimeout(finish, 300);
  }

  function two(n){ return String(n).padStart(2,'0'); }
  function fmtHora(d){ return two(d.getHours()) + ':' + two(d.getMinutes()); }
  function addMinutos(date, min){ return new Date(date.getTime() + min*60000); }
  function fmtDataLonga(d){
    const opt = { weekday:'long', day:'2-digit', month:'long' };
    return d.toLocaleDateString('pt-BR', opt);
  }
  function positionPopover(x, y){
    const W = sessionPopover.offsetWidth || 320, H = sessionPopover.offsetHeight || 260;
    const vw = window.innerWidth, vh = window.innerHeight, sy = window.scrollY, sx = window.scrollX;
    let left = x + 12 + sx, top = y + 12 + sy;
    if (left + W > vw + sx) left = vw - W - 12 + sx;
    if (top + H > vh + sy)  top  = vh - H - 12 + sy;
    if (left < 12 + sx) left = 12 + sx;
    if (top  < 12 + sy) top  = 12 + sy;
    sessionPopover.style.left = left + 'px';
    sessionPopover.style.top  = top  + 'px';
  }

  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeSessionPopover(); });
  document.addEventListener('click', (e)=>{
    if (sessionPopover.style.display !== 'none' && !sessionPopover.contains(e.target) && !e.target.closest('.fc-event')) {
      closeSessionPopover();
    }
  });
  sessionPopover.addEventListener('click', (e)=>{ e.stopPropagation(); });
  window.addEventListener('scroll', closeSessionPopover, { passive: true });

  const popupCSS = document.createElement('style');
  popupCSS.textContent = `
    .session-popover { opacity:0; transform:translateY(-8px) scale(0.985); visibility:hidden; pointer-events:none; will-change:opacity,transform; transition: opacity .22s ease, transform .22s ease, box-shadow .22s ease; }
    .session-popover.show { opacity:1; transform:translateY(0) scale(1); visibility:visible; pointer-events:auto; }
    .session-popover.hiding { opacity:0; transform:translateY(-6px) scale(0.985); pointer-events:none; }
    @media (prefers-reduced-motion: reduce){ .session-popover, .session-popover.show, .session-popover.hiding{ transition:none !important; transform:none !important; } }
    .session-popover .sp-head { display:flex; align-items:center; justify-content:space-between; gap:8px; padding:14px 16px; background:#f8fafc; border-bottom:1px solid #e2e8f0; }
    .session-popover .sp-title { display:flex; align-items:center; gap:10px; font-weight:800; color:#0f172a; margin:0; font-size:1.05rem; }
    .session-popover .sp-title .dot { width:12px; height:12px; border-radius:50%; display:inline-block; }
    .session-popover .sp-actions { display:flex; gap:6px; }
    .session-popover .icon-btn { border:none; background:transparent; padding:6px 8px; border-radius:8px; cursor:pointer; color:#64748b; transition:all .2s; }
    .session-popover .icon-btn:hover { background:#e2e8f0; color:#0f172a; }
    .session-popover .sp-body { padding:16px; display:grid; gap:14px; }
    .session-popover .row-line { display:flex; align-items:flex-start; gap:12px; }
    .session-popover .row-line i { font-size:1.1rem; color:#94a3b8; margin-top:2px; }
    .session-popover .muted { color:#64748b; font-size:.9rem; margin-top:2px; }
    .session-popover .link-btn { display:inline-flex; align-items:center; gap:8px; border:1px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; font-weight:700; padding:10px 14px; border-radius:10px; text-decoration:none; font-size:.9rem; transition:all .2s;}
    .session-popover .link-btn:hover { background:#dbeafe; }
    .session-popover .sp-footer { padding:14px 16px; border-top:1px solid #e2e8f0; display:flex; gap:8px; background:#fafafa; }
    .session-popover .pill { padding:6px 12px; border-radius:999px; font-size:.8rem; font-weight:800; border:1px solid transparent; }
  `;
  document.head.appendChild(popupCSS);

  async function abrirPopupSessao(info, clickX, clickY){
    clearTimeout(hideTimer);
    sessionPopover.classList.remove('hiding');

    const id = info.event.id;
    let sessao = null;

    try{
      const r = await fetch(`/sessoes-json/${id}`, {
        headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }
      });
      if (!r.ok) {
        const body = await r.json().catch(()=>({}));
        if (promptReconnectIfNeeded(body?.message, r.status)) return;
        throw new Error(body?.message || 'Erro ao carregar sessão');
      }
      sessao = await r.json();
    }catch(e){
      if (!promptReconnectIfNeeded(e?.message)) {
        Swal.fire('Erro', 'Não foi possível carregar os dados desta sessão.', 'error');
      }
      return;
    }

    const start = info.event.start;
    const dur   = (sessao?.duracao ?? 50);
    const end   = info.event.end || addMinutos(start, dur);

    const pacienteNome  = sessao?.paciente_nome ?? info.event.title ?? 'Paciente';
    const pacienteEmail = sessao?.paciente_email ?? '';
    const meetUrl       = sessao?.meet_url || '';
    const pago          = !!sessao?.foi_pago;
    
    const corDot = pago ? '#22c55e' : '#ef4444';
    const pillClass = pago 
        ? 'background: #dcfce7; color: #166534; border-color: #bbf7d0;' 
        : 'background: #fee2e2; color: #b91c1c; border-color: #fecaca;';

    const dataLonga = fmtDataLonga(start);
    const horaIni   = fmtHora(start);
    const horaFim   = fmtHora(end);

    sessionPopover.innerHTML = `
      <div class="sp-head">
        <h6 class="sp-title">
          <span class="dot" style="background:${corDot}"></span>
          <span class="text-truncate" style="max-width: 190px;">${pacienteNome}</span>
        </h6>
        <div class="sp-actions">
          <button class="icon-btn" id="sp-edit" title="Editar"><i class="bi bi-pencil-square"></i></button>
          <button class="icon-btn text-danger" id="sp-delete" title="Excluir"><i class="bi bi-trash3"></i></button>
          <button class="icon-btn" id="sp-close" title="Fechar"><i class="bi bi-x-lg"></i></button>
        </div>
      </div>

      <div class="sp-body">
        <div class="row-line">
          <i class="bi bi-calendar3"></i>
          <div>
            <div style="color: #0f172a; font-weight: 700;">${dataLonga}</div>
            <div class="muted">${horaIni} – ${horaFim} (BRT)</div>
          </div>
        </div>

        ${meetUrl ? `
          <div class="row-line">
            <i class="bi bi-camera-video text-primary"></i>
            <div class="d-flex align-items-center gap-2 w-100">
                <a class="link-btn flex-grow-1 justify-content-center" href="${meetUrl}" target="_blank" rel="noopener">
                <i class="bi bi-google"></i> Google Meet
                </a>
                <button class="icon-btn border" style="padding: 9px 12px; background: #fff;" id="sp-copy-meet" title="Copiar link"><i class="bi bi-link-45deg fs-5"></i></button>
            </div>
          </div>
        ` : ''}

        <div class="row-line">
          <i class="bi bi-person"></i>
          <div>
            <div style="color: #0f172a; font-weight: 700;">Detalhes do Paciente</div>
            ${pacienteEmail ? `<div class="muted">${pacienteEmail}</div>` : `<div class="muted">E-mail não cadastrado</div>`}
          </div>
        </div>
      </div>

      <div class="sp-footer">
        <span class="pill" style="${pillClass}">${pago ? '<i class="bi bi-check-circle me-1"></i> Pago' : '<i class="bi bi-exclamation-circle me-1"></i> Pagamento Pendente'}</span>
      </div>
    `;

    sessionPopover.style.display = 'block';
    void sessionPopover.offsetWidth; 
    positionPopover(clickX, clickY);
    requestAnimationFrame(() => sessionPopover.classList.add('show'));

    document.getElementById('sp-close')?.addEventListener('click', (e)=>{ e.stopPropagation(); closeSessionPopover(); });

    if (meetUrl) {
      document.getElementById('sp-copy-meet')?.addEventListener('click', async (e)=>{
        e.stopPropagation();
        try{
          await navigator.clipboard.writeText(meetUrl);
          Swal.fire({ icon:'success', title:'Link copiado', timer:1200, showConfirmButton:false, toast: true, position: 'top-end' });
        }catch(_){}
      });
    }

    document.getElementById('sp-edit')?.addEventListener('click', async (e)=>{
      e.stopPropagation();
      try{
        campos.id.value        = sessao.id;
        campos.paciente.value  = sessao.paciente_id;
        campos.data_hora.value = sessao.data_hora;
        campos.valor.value     = sessao.valor;
        campos.duracao.value   = sessao.duracao ?? 50;
        campos.foi_pago.checked= !!sessao.foi_pago;
        campos.titulo.innerText= "Editar Sessão";
        closeSessionPopover();
        modal.show();
      }catch{
        Swal.fire('Erro', 'Não foi possível carregar a sessão para edição.', 'error');
      }
    });

    document.getElementById('sp-delete')?.addEventListener('click', async (e)=>{
      e.stopPropagation();
      const ok = await Swal.fire({
        icon:'warning', title:'Excluir sessão?', text:'Esta ação não pode ser desfeita.',
        showCancelButton:true, confirmButtonText:'Excluir', cancelButtonText:'Cancelar',
        confirmButtonColor:'#dc2626'
      }).then(r=>r.isConfirmed);
      if(!ok) return;

      const csrf = document.querySelector('input[name="_token"]')?.value
                || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      try{
        const res = await fetch(`/sessoes-json/${id}`, {
          method:'DELETE',
          headers:{
            'Accept':'application/json',
            'X-Requested-With':'XMLHttpRequest',
            ...(csrf ? {'X-CSRF-TOKEN': csrf} : {})
          }
        });

        let body = {};
        const text = await res.text();
        try { body = text ? JSON.parse(text) : {}; } catch(_) { body = {}; }

        if(!res.ok){
          if (promptReconnectIfNeeded(body?.message, res.status)) return;
          if (res.status === 419){
            Swal.fire('Sessão expirada', 'Atualize a página e tente novamente.', 'warning');
            return;
          }
          throw new Error(body?.message || 'Erro ao excluir');
        }

        closeSessionPopover();
        calendar.refetchEvents();
        Swal.fire({ icon:'success', title:'Sessão excluída', timer:1400, showConfirmButton:false, toast:true, position:'top-end' });
      }catch(e){
        if (!promptReconnectIfNeeded(e?.message)) {
          Swal.fire('Erro', e?.message || 'Falha ao excluir a sessão.', 'error');
        }
      }
    });
  }

  const feriadosDatasCache  = {};
  const feriadosNomesCache  = {};

  async function carregarFeriadosAno(ano) {
    if (feriadosDatasCache[ano]) return;
    try {
      const resp = await fetch(`/api/feriados?ano=${ano}&full=1`, { headers: { 'Accept': 'application/json', 'X-Requested-With':'XMLHttpRequest' }});
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      let data = await resp.json();
      if (!Array.isArray(data) && Array.isArray(data?.holidays)) data = data.holidays;

      if (Array.isArray(data) && data.length && typeof data[0] === 'string') {
        feriadosDatasCache[ano] = new Set(data);
        feriadosNomesCache[ano] = new Map();
        return;
      }

      const set = new Set(); const map = new Map();
      if (Array.isArray(data)) {
        for (const f of data) {
          if (!f) continue;
          const dt = f.data || f.date || f.date_iso || f.dia || f?.date?.split('T')?.[0];
          const nm = f.nome || f.name || f.titulo || null;
          if (!dt) continue;
          set.add(dt); if (nm) map.set(dt, nm);
        }
      }
      feriadosDatasCache[ano] = set; feriadosNomesCache[ano] = map;
    } catch (e) {
      feriadosDatasCache[ano] = new Set(); feriadosNomesCache[ano] = new Map();
    }
  }

  function toYMDLocal(d){ return new Date(d.getTime() - d.getTimezoneOffset()*60000).toISOString().slice(0,10); }
  function isFeriado(dateObj){ const y=dateObj.getFullYear(); const ymd=toYMDLocal(dateObj); const set=feriadosDatasCache[y]; return !!(set && set.has(ymd)); }
  function nomeFeriado(dateObj){ const y=dateObj.getFullYear(); const ymd=toYMDLocal(dateObj); const map=feriadosNomesCache[y]; return map ? map.get(ymd) : undefined; }

  await carregarFeriadosAno(new Date().getFullYear());

  const prettyTitle = (t) => t.replace(/ de /g, ' · ');

  // ATENÇÃO: Aqui os plugins e o locale originais foram restaurados
  const calendar = new window.FullCalendar.Calendar(calendarEl, {
    plugins: [
        window.FullCalendar.dayGridPlugin,
        window.FullCalendar.timeGridPlugin,
        window.FullCalendar.interactionPlugin,
        window.FullCalendar.bootstrap5Plugin
    ],
    themeSystem: 'bootstrap5',
    timeZone: 'local',
    height: 700,
    locale: window.FullCalendar.ptBr,
    initialView: 'dayGridMonth',
    headerToolbar: false,
    events: '/api/sessoes',

    datesSet: async function(info) {
      if (calendarH1) calendarH1.innerHTML = `<span>${prettyTitle(info.view.title)}</span>`;
      syncActiveViewButton(info.view.type);

      const startYear = info.start.getFullYear();
      const endYear   = new Date(info.end.getTime() - 1).getFullYear();
      for (let y = startYear; y <= endYear; y++) await carregarFeriadosAno(y);
      calendar.render();
    },

    dayCellDidMount: function(arg) {
      const y = arg.date.getFullYear();
      carregarFeriadosAno(y).then(() => {
        if (isFeriado(arg.date)) {
          arg.el.classList.add('pg-feriado');
          const nome = nomeFeriado(arg.date);
          if (nome) {
            const target = arg.el.querySelector('.fc-daygrid-day-number') || arg.el;
            target.setAttribute('title', nome);
          }
        }
      });
    },

    eventContent: function (arg) {
      const viewType = arg.view.type;
      const event = arg.event;
      const fmt = (d) => two(d.getHours()) + ':' + two(d.getMinutes());
      const hi = fmt(event.start);
      const hf = event.end ? fmt(event.end) : '';
      const t  = event.title;
      return (viewType === 'dayGridMonth')
        ? { html: `<div class="text-truncate"><i class="bi bi-clock me-1 opacity-75"></i>${hi} - ${t}</div>` }
        : { html: `<div class="fw-bold opacity-75 mb-1">${hi} - ${hf}</div><div class="text-truncate">${t}</div>` };
    },

    dateClick: async function(info) {
      const d = info.date; const y = d.getFullYear();
      await carregarFeriadosAno(y);
      if (isFeriado(d)) {
        const nome = nomeFeriado(d);
        const { isConfirmed } = await Swal.fire({
          icon: 'info',
          title: nome ? `Feriado: ${nome}` : 'Feriado',
          text: 'Deseja continuar e agendar uma sessão para este dia?',
          showCancelButton: true,
          confirmButtonText: 'Sim, agendar',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#2563eb'
        });
        if (!isConfirmed) return;
      }
      abrirModalCriar(info.dateStr);
    },

    eventClick: function(info){
      info.jsEvent.preventDefault();
      closeSessionPopover();
      const { clientX, clientY } = info.jsEvent;
      abrirPopupSessao(info, clientX, clientY);
    },

    eventDidMount(info){
      const tooltip = info.event.extendedProps.tooltip;
      if (tooltip) info.el.setAttribute('title', tooltip);
    }
  });

  calendar.render();

  document.getElementById('prevBtn')?.addEventListener('click', () => calendar.prev());
  document.getElementById('nextBtn')?.addEventListener('click', () => calendar.next());
  document.getElementById('todayBtn')?.addEventListener('click', () => calendar.today());

  const viewButtons = { monthBtn:'dayGridMonth', weekBtn:'timeGridWeek', dayBtn:'timeGridDay' };
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
    const activeId =
      currentView === 'dayGridMonth' ? 'monthBtn' :
      currentView === 'timeGridWeek' ? 'weekBtn'  :
      currentView === 'timeGridDay'  ? 'dayBtn'   : null;
    if (activeId) document.getElementById(activeId)?.classList.add('active');
  }

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
    const id   = campos.id.value;
    const rota = id ? `/sessoes-json/${id}` : `/sessoes-json`;
    const metodo = id ? 'PUT' : 'POST';
    const payload = {
      paciente_id: campos.paciente.value,
      data_hora:   campos.data_hora.value,
      valor:       campos.valor.value,
      duracao:     parseInt(campos.duracao.value),
      foi_pago:    campos.foi_pago.checked ? 1 : 0,
    };

    const csrf = document.querySelector('input[name="_token"]')?.value
              || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
      const response = await fetch(rota, {
        method: metodo,
        headers: {
          'X-CSRF-TOKEN': csrf || '',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload)
      });
      const resData = await response.json().catch(()=>({}));

      if (!response.ok) {
        if (promptReconnectIfNeeded(resData?.message, response.status)) return;
        if (response.status === 419){
          Swal.fire('Sessão expirada', 'Atualize a página e tente novamente.', 'warning');
        } else if (resData?.message?.includes("Conflito de horário")) {
          Swal.fire({ icon:'error', title:'Conflito de Horário', text:resData.message, confirmButtonColor:'#2563eb' });
        } else {
            Swal.fire('Erro', resData?.message || 'Erro ao salvar a sessão.', 'error');
        }
        if (typeof hideSpinner === 'function') hideSpinner();
        return;
      }

      modal.hide();
      closeSessionPopover();
      calendar.refetchEvents();
      
      Swal.fire({ 
          icon:'success', 
          title:'Sucesso!', 
          text: id ? 'Sessão atualizada com sucesso!' : 'Sessão criada com sucesso!', 
          timer:1800, 
          showConfirmButton:false,
          toast: true,
          position: 'top-end'
      }).then(() => { if (typeof hideSpinner === 'function') hideSpinner(); });

    } catch (error) {
      if (!promptReconnectIfNeeded(error?.message)) {
        Swal.fire('Erro', error.message || 'Erro inesperado ao salvar a sessão.', 'error')
          .then(() => { if (typeof hideSpinner === 'function') hideSpinner(); });
      }
    }
  });
});
</script>
@endsection