@extends('layouts.app')

@section('title', 'Agenda | PsiGestor')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root{
        --pg-primary:#12B4B7;
        --pg-primary-600:#0ea2a5;
        --pg-primary-700:#0b7f83;
        --pg-ink:#15181f;
        --pg-muted:#6c7685;
        --pg-border:#e6eaef;
        --pg-surface:#ffffff;

        --pg-weekend:#FFF6EA;
        --pg-holiday:#ECF7FF;
        --pg-holiday-border:#BEE3FF;
    }

    #calendar-card{
        border:1px solid var(--pg-border);
        border-radius:16px;
        background:var(--pg-surface);
        padding:14px;
        box-shadow:0 6px 18px rgba(10,20,30,.06);
    }

    .calendar-header{
        position:relative;
        background:var(--pg-surface);
        border:1px solid var(--pg-border);
        border-radius:16px;
        padding:12px;
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

    .calendar-row{ display:grid; gap:10px; grid-template-columns:1fr; }

    .cal-title{ display:flex; gap:8px; align-items:center; justify-content:center; text-align:center; }
    .cal-icon{
        width:30px; height:30px; border-radius:8px; display:grid; place-items:center;
        background:rgba(18,180,183,.10); color:var(--pg-primary); font-size:16px; flex:0 0 auto;
    }
    .cal-text .cal-head{ font-weight:800; letter-spacing:.2px; color:var(--pg-ink); line-height:1.1; font-size:1.05rem; }

    /* chips */
    .pg-chip{
        display:inline-flex; align-items:center; gap:6px;
        border:1px solid #cfe7e8; background:rgba(18,180,183,.10); color:#0b7f83;
        padding:5px 10px; border-radius:999px; font-weight:700; font-size:.8rem; line-height:1; text-decoration:none; white-space:nowrap;
    }
    .pg-chip:hover{ background:rgba(18,180,183,.14) }
    .pg-chip i{font-size:.95rem}
    .chip-success{ background:rgba(16,185,129,.14); border-color:rgba(16,185,129,.35); color:#0e7a56; }
    .chip-warn{ background:rgba(234,88,12,.14); border-color:rgba(234,88,12,.35); color:#8e3a0d; }

    /* ===== header (como o 2º print) ===== */
    .cal-sub{ display:flex; flex-direction:column; gap:10px; }
    .connect-row{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    .sync-row{ display:flex; gap:12px; flex-wrap:wrap; }
    .sync-row .btn-outline{
        background:#fff; border:1px solid var(--pg-primary); color:var(--pg-primary);
        border-radius:12px; font-weight:800; padding:10px 14px; min-height:44px; min-width:230px;
    }

    .legend{ display:flex; flex-wrap:wrap; gap:6px; align-items:center; justify-content:center; }
    .legend .pg-chip{ padding:5px 8px; font-weight:700; font-size:.78rem; }
    .legend .dot{ width:10px; height:10px; border-radius:999px; display:inline-block; margin-right:6px; box-shadow:0 0 0 2px #fff, 0 0 0 3px #cfe7e8; }
    .dot-paid{ background:#28a745 } .dot-pending{ background:#dc3545 } .dot-late{ background:#00c4ff }

    .cal-actions { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .spacer { flex:1; }
    .btn-group-clean{ display:flex; gap:8px; align-items:center; }
    .btn-ghost,.btn-brand,.btn-outline{
        display:inline-flex; align-items:center; justify-content:center; gap:6px;
        border-radius:12px; font-weight:800; letter-spacing:.2px;
        padding:10px 14px; font-size:.92rem; min-height:44px; transition:.15s;
        width:100%;
    }
    .btn-ghost{ background:#eef5f6; border:1px solid #cfe7e8; color:#184146; }
    .btn-ghost:hover{ transform:translateY(-1px); box-shadow:0 6px 14px rgba(10,20,30,.10) }
    .btn-brand{ background:var(--pg-primary); border:1px solid var(--pg-primary-700); color:#fff; box-shadow:0 8px 18px rgba(18,180,183,.28); }
    .btn-brand:hover{ transform:translateY(-1px); }
    .nav-switch,.view-switch{ display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
    .view-switch .vbtn,.nav-switch .nbtn{
        border:1px solid #cfe7e8; background:#fff; color:#1f2a33; border-radius:12px; padding:9px 12px; font-weight:800; font-size:.92rem; min-height:44px;
    }
    .view-switch .vbtn.active{ background:linear-gradient(180deg,#fff,#f0feff); border-color:var(--pg-primary); box-shadow:0 8px 18px rgba(18,180,183,.22); color:var(--pg-primary); }

    .cal-right{ display:flex; justify-content:flex-end; align-items:center; }

    @media (max-width:767.98px){
    .cal-title{ justify-content:center; text-align:center; }
    .cal-sub{ align-items:center; }
    .connect-row, .sync-row{ justify-content:center; }   /* chips e botões de sync centralizados */

    .cal-right{ justify-content:center; }                /* área dos controles à direita */
    .cal-actions{ justify-content:center; align-items:center; }
    .nav-switch, .view-switch, .cal-actions .btn-group-clean{ justify-content:center; }
    .spacer{ display:none; }                             /* remove o “empurra” no mobile */

    /* nos controles da direita, os botões não ocupam 100% no mobile */
    .cal-actions .btn-ghost,
    .cal-actions .btn-brand,
    .cal-actions .btn-outline,
    .cal-actions .vbtn,
    .cal-actions .nbtn{ width:auto; } /* empilha bem no mobile */ 
}

    @media (min-width: 768px){
        #calendar-card{ padding:18px; }
        .calendar-row{ grid-template-columns:1fr auto; align-items:center; }
        .cal-title{ justify-content:flex-start; text-align:left; }
        .cal-text .cal-head{ font-size:1.25rem; }
        .cal-actions{ flex-wrap:nowrap; }
        .nav-switch,.view-switch{ display:flex; }
        .nav-switch .nbtn,.view-switch .vbtn{ min-width:auto; }
        .btn-ghost,.btn-brand,.btn-outline{ width:auto; }
    }
    @media (min-width: 992px){ .cal-text .cal-head{ font-size:1.35rem; } }

    /* ===== FullCalendar ===== */
    .fc .fc-day-sat, .fc .fc-day-sun{ background: var(--pg-weekend); }
    .fc .pg-feriado{ background: var(--pg-holiday) !important; box-shadow: inset 0 0 0 1px var(--pg-holiday-border); }
    .fc-daygrid-day.pg-feriado .fc-daygrid-day-number::after{
        content: "Feriado"; display:inline-block; margin-left:6px; padding:2px 6px;
        font-size:.65rem; font-weight:700; color:#0b6aa8; background:#dff1ff;
        border:1px solid var(--pg-holiday-border); border-radius:999px; vertical-align:middle;
    }
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

    /* ===== Popup de sessão (card flutuante) ===== */
    .session-popover {
    position: absolute;
    z-index: 1060; /* acima do calendário e abaixo do modal */
    width: 320px;
    max-width: calc(100vw - 24px);
    background: #fff;
    border: 1px solid var(--pg-border);
    border-radius: 14px;
    box-shadow: 0 12px 28px rgba(10,20,30,.18);
    overflow: hidden;

    /* --- animação / estados --- */
    opacity: 0;
    transform: translateY(-8px) scale(0.985);
    visibility: hidden;
    pointer-events: none;
    will-change: opacity, transform;
    transition:
        opacity 0.22s ease,
        transform 0.22s ease,
        box-shadow 0.22s ease;
    }

    /* Estado visível (abre suave) */
    .session-popover.show {
    opacity: 1;
    transform: translateY(0) scale(1);
    visibility: visible;
    pointer-events: auto;
    }

    /* Estado de saída (fecha suave) */
    .session-popover.hiding {
    opacity: 0;
    transform: translateY(-6px) scale(0.985);
    pointer-events: none;
    }

    /* Acessibilidade: reduz movimento se o usuário preferir */
    @media (prefers-reduced-motion: reduce) {
    .session-popover,
    .session-popover.show,
    .session-popover.hiding {
        transition: none !important;
        transform: none !important;
    }
    }

    /* Cabeçalho / conteúdo */
    .session-popover .sp-head{
    display:flex; align-items:center; justify-content:space-between;
    gap:8px; padding:10px 12px; border-bottom:1px solid var(--pg-border);
    }
    .session-popover .sp-title{
    display:flex; align-items:center; gap:8px; font-weight:700; color:#111; margin:0;
    }
    .session-popover .sp-title .dot{
    width:10px; height:10px; border-radius:999px; display:inline-block;
    }
    .session-popover .sp-actions{ display:flex; gap:6px; }
    .session-popover .icon-btn{
    border:none; background:transparent; padding:6px; border-radius:8px; cursor:pointer;
    }
    .session-popover .icon-btn:hover{ background:#f3f6f8; }
    .session-popover .sp-body{ padding:10px 12px; display:grid; gap:10px; }
    .session-popover .row-line{ display:flex; align-items:center; gap:10px; }
    .session-popover .row-line i{ font-size:1rem; color:#555; }
    .session-popover .muted{ color:#6b7280; font-size:.92rem; }
    .session-popover .link-btn{
    display:inline-flex; align-items:center; gap:8px;
    border:1px solid #e5e7eb; padding:8px 10px; border-radius:10px; text-decoration:none;
    }
    .session-popover .sp-footer{ padding:10px 12px; border-top:1px solid var(--pg-border); display:flex; gap:8px; flex-wrap:wrap; }
    .session-popover .pill{
    background:#f5f7fa; border:1px solid #e5e7eb; padding:6px 10px; border-radius:999px; font-size:.85rem;
    }

</style>
@endsection

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="calendar-header">
        <div class="calendar-row">
            <div class="cal-left">
                <div class="cal-title">
                    <div class="cal-icon"><i class="bi bi-calendar3-event"></i></div>
                    <div class="cal-text">
                        <div class="cal-head"><span id="calendarTitle">Minha Agenda</span></div>

                        <div class="cal-sub mt-2">
                            @if(auth()->user()?->google_connected)
                                <!-- linha 1: status + desconectar -->
                                <div class="connect-row">
                                    <span class="pg-chip chip-success">
                                        <i class="bi bi-google"></i> Google Agenda conectado
                                    </span>
                                    <form action="{{ route('google.disconnect') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="pg-chip" title="Desconectar Google">
                                            <i class="bi bi-x-circle"></i> Desconectar
                                        </button>
                                    </form>
                                </div>

                                <!-- linha 2: botões lado-a-lado -->
                                <div class="sync-row">
                                    <form action="{{ route('sessoes.sync.futuras') }}" method="POST" class="d-inline">@csrf
                                        <button type="submit" class="btn-outline">
                                            <i class="bi bi-arrow-repeat me-1"></i> Sincronizar futuras
                                        </button>
                                    </form>
                                    <form action="{{ route('sessoes.sync.todas') }}" method="POST" class="d-inline">@csrf
                                        <button type="submit" class="btn-outline">
                                            <i class="bi bi-cloud-arrow-up me-1"></i> Sincronizar todas
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="connect-row">
                                    <span class="pg-chip chip-warn">
                                        <i class="bi bi-exclamation-triangle"></i> Google não conectado
                                    </span>
                                    <a href="{{ route('google.connect') }}" class="pg-chip">
                                        <i class="bi bi-google me-1"></i> Conectar ao Google
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="cal-right">
                <div class="cal-actions">
                    <div class="btn-group-clean nav-switch">
                        <button id="prevBtn" class="nbtn btn-ghost" aria-label="Mês anterior">←</button>
                        <button id="todayBtn" class="nbtn btn-ghost">Hoje</button>
                        <button id="nextBtn" class="nbtn btn-ghost" aria-label="Próximo mês">→</button>
                    </div>

                    <div class="btn-group-clean view-switch">
                        <button id="monthBtn" class="vbtn active">Mês</button>
                        <button id="weekBtn" class="vbtn">Semana</button>
                        <button id="dayBtn" class="vbtn">Dia</button>
                    </div>

                    <div class="spacer"></div>

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

    <div id="calendar-card"><div id="calendar"></div></div>

    <div class="legend mt-3">
        <span class="pg-chip"><span class="dot dot-paid"></span> Pago</span>
        <span class="pg-chip"><span class="dot dot-pending"></span> Pendente</span>
        <span class="pg-chip"><i class="bi bi-moon-fill text-warning"></i> Após 00:00</span>
    </div>
</div>

<!-- Container para o popup de sessão (inicialmente vazio) -->
<div id="session-popover" class="session-popover" style="display:none;" role="dialog" aria-modal="true" aria-live="polite"></div>

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
                <input type="datetime-local" name="data_hora" id="data_hora" class="form-control form-control-sm shadow-sm" required>
            </div>
            <div class="col-6">
                <label class="form-label small text-muted fw-semibold">Valor (R$)</label>
                <input type="number" step="0.01" name="valor" id="valor" class="form-control form-control-sm shadow-sm" required>
            </div>
            <div class="col-6">
                <label class="form-label small text-muted fw-semibold">Duração (min)</label>
                <input type="number" name="duracao" id="duracao" class="form-control form-control-sm shadow-sm" value="50" required>
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

<script>
document.addEventListener('DOMContentLoaded', async function () {
  const calendarEl = document.getElementById('calendar');
  const calendarH1 = document.getElementById('calendarTitle');
  const modal      = new bootstrap.Modal(document.getElementById('modalSessao'));

  const campos = {
    id:document.getElementById('sessao_id'),
    paciente:document.getElementById('paciente_id'),
    data_hora:document.getElementById('data_hora'),
    valor:document.getElementById('valor'),
    duracao:document.getElementById('duracao'),
    foi_pago:document.getElementById('foi_pago'),
    titulo:document.getElementById('modalTitulo'),
  };

  if (!window.FullCalendar || !calendarEl) return;

  /** ===== Helper: pede reconexão do Google quando necessário ===== */
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

  /* ===================== POPUP (card flutuante) ===================== */
  let sessionPopover = document.getElementById('session-popover');
  if (!sessionPopover) {
    sessionPopover = document.createElement('div');
    sessionPopover.id = 'session-popover';
    sessionPopover.className = 'session-popover';
    // base (sem animação; as transições virão via <style> abaixo)
    sessionPopover.style.cssText =
      'display:none;position:absolute;z-index:1060;width:320px;max-width:calc(100vw - 24px);background:#fff;border:1px solid #e6eaef;border-radius:14px;box-shadow:0 12px 28px rgba(10,20,30,.18);overflow:hidden';
    // Acessibilidade
    sessionPopover.setAttribute('role','dialog');
    sessionPopover.setAttribute('aria-modal','true');
    sessionPopover.setAttribute('aria-live','polite');
    document.body.appendChild(sessionPopover);
  }

  let hideTimer = null;

function closeSessionPopover(){
  // se já está escondido, não faz nada
  if (sessionPopover.style.display === 'none') return;

  // inicia animação de saída
  sessionPopover.classList.remove('show');
  sessionPopover.classList.add('hiding');

  const finish = () => {
    sessionPopover.style.display = 'none';
    sessionPopover.classList.remove('hiding'); // <- IMPORTANTÍSSIMO
    sessionPopover.innerHTML = '';
  };

  // tenta ouvir o fim da transição...
  sessionPopover.addEventListener('transitionend', finish, { once:true });

  // ...mas garante com fallback, caso 'transitionend' não dispare
  clearTimeout(hideTimer);
  hideTimer = setTimeout(finish, 300); // >= ao tempo da sua transition (0.22s)
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

  // Fecha com ESC
  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeSessionPopover(); });
  // Fecha ao clicar fora; impede fechar ao clicar dentro
  document.addEventListener('click', (e)=>{
    if (sessionPopover.style.display !== 'none' &&
        !sessionPopover.contains(e.target) &&
        !e.target.closest('.fc-event')) {
      closeSessionPopover();
    }
  });
  // evita “fechar fora” se clicar dentro do popover
  sessionPopover.addEventListener('click', (e)=>{ e.stopPropagation(); });
  // fecha ao rolar (evita ficar mal posicionado)
  window.addEventListener('scroll', closeSessionPopover, { passive: true });

  // Estilos do popup + ANIMAÇÃO
  const popupCSS = document.createElement('style');
  popupCSS.textContent = `
    .session-popover{
      opacity:0; transform:translateY(-8px) scale(0.985);
      visibility:hidden; pointer-events:none;
      will-change:opacity,transform;
      transition: opacity .22s ease, transform .22s ease, box-shadow .22s ease;
    }
    .session-popover.show{
      opacity:1; transform:translateY(0) scale(1);
      visibility:visible; pointer-events:auto;
    }
    .session-popover.hiding{
      opacity:0; transform:translateY(-6px) scale(0.985);
      pointer-events:none;
    }
    @media (prefers-reduced-motion: reduce){
      .session-popover, .session-popover.show, .session-popover.hiding{ transition:none !important; transform:none !important; }
    }
    .session-popover .sp-head{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:10px 12px;border-bottom:1px solid #e6eaef}
    .session-popover .sp-title{display:flex;align-items:center;gap:8px;font-weight:700;color:#111;margin:0}
    .session-popover .sp-title .dot{width:10px;height:10px;border-radius:999px;display:inline-block}
    .session-popover .sp-actions{display:flex;gap:6px}
    .session-popover .icon-btn{border:none;background:transparent;padding:6px;border-radius:8px;cursor:pointer}
    .session-popover .icon-btn:hover{background:#f3f6f8}
    .session-popover .sp-body{padding:10px 12px;display:grid;gap:10px}
    .session-popover .row-line{display:flex;align-items:center;gap:10px}
    .session-popover .row-line i{font-size:1rem;color:#555}
    .session-popover .muted{color:#6b7280;font-size:.92rem}
    .session-popover .link-btn{display:inline-flex;align-items:center;gap:8px;border:1px solid #e5e7eb;padding:8px 10px;border-radius:10px;text-decoration:none}
    .session-popover .sp-footer{padding:10px 12px;border-top:1px solid #e6eaef;display:flex;gap:8px;flex-wrap:wrap}
    .session-popover .pill{background:#f5f7fa;border:1px solid #e5e7eb;padding:6px 10px;border-radius:999px;font-size:.85rem}
  `;
  document.head.appendChild(popupCSS);

  async function abrirPopupSessao(info, clickX, clickY){
    // garante que não há estado "hiding" preso de um fechamento anterior
    clearTimeout(hideTimer);
    sessionPopover.classList.remove('hiding');

    const id = info.event.id;

    // Busca dados completos no backend
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
    const meetUrl       = sessao?.meet_url || ''; // apenas do backend
    const pago          = !!sessao?.foi_pago;
    const corDot        = pago ? '#28a745' : '#dc3545';

    const dataLonga = fmtDataLonga(start);
    const horaIni   = fmtHora(start);
    const horaFim   = fmtHora(end);

    sessionPopover.innerHTML = `
      <div class="sp-head">
        <h6 class="sp-title">
          <span class="dot" style="background:${corDot}"></span>
          <span>Sessão com ${pacienteNome}</span>
        </h6>
        <div class="sp-actions">
          <button class="icon-btn" id="sp-edit" title="Editar"><i class="bi bi-pencil"></i></button>
          <button class="icon-btn" id="sp-delete" title="Excluir"><i class="bi bi-trash"></i></button>
          <button class="icon-btn" id="sp-close" title="Fechar"><i class="bi bi-x-lg"></i></button>
        </div>
      </div>

      <div class="sp-body">
        <div class="row-line"><i class="bi bi-calendar-event"></i>
          <div>
            <div><strong>${dataLonga}</strong></div>
            <div class="muted">${horaIni} – ${horaFim}</div>
          </div>
        </div>

        ${meetUrl ? `
          <div class="row-line">
            <i class="bi bi-camera-video"></i>
            <a class="link-btn" href="${meetUrl}" target="_blank" rel="noopener">
              <i class="bi bi-google"></i> Entrar com o Google Meet
            </a>
            <button class="icon-btn" id="sp-copy-meet" title="Copiar link"><i class="bi bi-clipboard"></i></button>
          </div>
        ` : ''}

        <div class="row-line">
          <i class="bi bi-people"></i>
          <div>
            <div class="muted">1 convidado • pendente</div>
            ${pacienteEmail ? `<div>${pacienteEmail}</div>` : ``}
          </div>
        </div>
      </div>

      <div class="sp-footer">
        <span class="pill">${pago ? 'Pago' : 'Pendente'}</span>
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
          Swal.fire({ icon:'success', title:'Link copiado', timer:1200, showConfirmButton:false });
        }catch(_){}
      });
    }

    // Editar
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

    // Excluir (robusto: 204/200/JSON, 419 CSRF, 401/403 reconexão)
    document.getElementById('sp-delete')?.addEventListener('click', async (e)=>{
      e.stopPropagation();
      const ok = await Swal.fire({
        icon:'warning', title:'Excluir sessão?', text:'Esta ação não pode ser desfeita.',
        showCancelButton:true, confirmButtonText:'Excluir', cancelButtonText:'Cancelar',
        confirmButtonColor:'#dc3545'
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

        // Pode vir 204 sem corpo, 200 com JSON, ou 4xx com JSON/HTML
        let body = {};
        const text = await res.text();
        try { body = text ? JSON.parse(text) : {}; } catch(_) { body = {}; }

        if(!res.ok){
          if (promptReconnectIfNeeded(body?.message, res.status)) return;
          if (res.status === 419){ // CSRF
            Swal.fire('Sessão expirada', 'Atualize a página e tente novamente.', 'warning');
            return;
          }
          throw new Error(body?.message || 'Erro ao excluir');
        }

        closeSessionPopover();
        calendar.refetchEvents();
        Swal.fire({ icon:'success', title:'Sessão excluída', timer:1400, showConfirmButton:false });
      }catch(e){
        if (!promptReconnectIfNeeded(e?.message)) {
          Swal.fire('Erro', e?.message || 'Falha ao excluir a sessão.', 'error');
        }
      }
    });
  }
  /* =================== /POPUP =================== */

  /* =================== FERIADOS =================== */
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
  /* =================== /FERIADOS =================== */

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
        ? { html: `<div>${hi} - ${t}</div>` }
        : { html: `<div>${hi} - ${hf}</div><div>${t}</div>` };
    },

    dateClick: async function(info) {
      const d = info.date; const y = d.getFullYear();
      await carregarFeriadosAno(y);
      if (isFeriado(d)) {
        const nome = nomeFeriado(d);
        const { isConfirmed } = await Swal.fire({
          icon: 'info',
          title: nome ? `Feriado: ${nome}` : 'Feriado',
          text: 'Deseja continuar e criar uma sessão para este dia?',
          showCancelButton: true,
          confirmButtonText: 'Continuar',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#12B4B7'
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
          Swal.fire({ icon:'error', title:'Conflito de Horário', text:resData.message, confirmButtonColor:'#3085d6' });
        } else {
          Swal.fire('Erro', resData?.message || 'Erro ao salvar a sessão.', 'error');
        }
        if (typeof hideSpinner === 'function') hideSpinner();
        return;
      }

      modal.hide();
      closeSessionPopover();
      calendar.refetchEvents();
      Swal.fire({ icon:'success', title:'Sucesso!', text: id ? 'Sessão atualizada com sucesso!' : 'Sessão criada com sucesso!', timer:1800, showConfirmButton:false })
        .then(() => { if (typeof hideSpinner === 'function') hideSpinner(); });

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
