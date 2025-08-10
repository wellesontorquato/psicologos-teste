@extends('layouts.app')

@section('title', 'Agenda | PsiGestor')

@section('styles')
<style>
    /*
    ============================================
    ESTILO VISUAL INTEGRADO - PSIGESTOR
    Baseado na imagem fornecida para máxima fidelidade.
    ============================================
    */

    /* Variáveis de Cor baseadas no seu layout */
    :root {
        --psi-bg-main: #f8f9fa; /* Fundo principal da área de conteúdo */
        --psi-text-primary: #212529; /* Títulos e textos principais */
        --psi-text-secondary: #6c757d; /* Textos secundários e placeholders */
        --psi-border-color: #dee2e6; /* Bordas suaves */
        
        --psi-button-bg: #f1f3f5; /* Fundo do botão padrão */
        --psi-button-hover: #e9ecef; /* Hover do botão */
        --psi-button-active-bg: #343a40; /* Fundo do botão ativo (escuro) */
        --psi-button-active-text: #ffffff; /* Texto do botão ativo (branco) */
        
        --psi-google-connect: #007bff; /* Cor para o botão de conectar ao Google */
        --psi-google-disconnect: #dc3545; /* Cor para o botão de desconectar */
        
        --psi-shadow: rgba(0, 0, 0, 0.05);
    }

    /* Remove o fundo padrão do container para usar o do body ou wrapper */
    .container {
        max-width: 100%; /* Ocupa todo o espaço do painel de conteúdo */
    }

    /* Cabeçalho da Página */
    .page-header {
        margin-bottom: 2rem;
    }

    .calendar-main-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--psi-text-primary);
        margin-bottom: 0.5rem;
    }

    .calendar-main-title i {
        font-size: 1.5rem;
        color: var(--psi-text-secondary);
    }
    
    .calendar-sub-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-left: 42px; /* Alinha com o texto do título */
    }

    /* Barra de Controles */
    .calendar-controls-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap; /* Permite quebrar linha em telas menores */
        gap: 1rem;
        padding: 0.75rem;
        background-color: #ffffff;
        border: 1px solid var(--psi-border-color);
        border-radius: 0.5rem;
        margin-top: 1.5rem;
    }

    .calendar-controls-bar .control-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Estilo dos botões para replicar o da imagem */
    .calendar-controls-bar .btn {
        background-color: var(--psi-button-bg);
        border: 1px solid var(--psi-border-color);
        color: var(--psi-text-primary);
        font-weight: 500;
        padding: 0.375rem 0.85rem;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }
    .calendar-controls-bar .btn:hover {
        background-color: var(--psi-button-hover);
    }
    .calendar-controls-bar .btn.active {
        background-color: var(--psi-button-active-bg);
        color: var(--psi-button-active-text);
        border-color: var(--psi-button-active-bg);
    }

    /* Botões específicos do Google com texto */
    .btn-google-connect {
        background-color: var(--psi-google-connect);
        color: white;
        border-color: var(--psi-google-connect);
    }
    .btn-google-connect:hover {
        background-color: #0056b3;
        color: white;
    }
    .btn-google-disconnect {
        background-color: transparent;
        color: var(--psi-google-disconnect);
        border: 1px solid var(--psi-google-disconnect);
    }
    .btn-google-disconnect:hover {
        background-color: var(--psi-google-disconnect);
        color: white;
    }


    /* Card do Calendário */
    #calendar-card {
        background: #ffffff;
        border: 1px solid var(--psi-border-color);
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px var(--psi-shadow);
    }

    /* Estilo dos eventos (mantido do original por ser bom) */
    .fc-event {
        border-radius: 4px !important;
        padding: 4px 6px !important;
        font-size: 0.85rem !important;
        cursor: pointer;
    }
    .fc .fc-event.evento-pago {
        background: #28a745 !important;
        border: 1px solid #218838 !important;
        color: white !important;
    }
    .fc .fc-event.evento-pendente {
        background: #dc3545 !important;
        border: 1px solid #c82333 !important;
        color: white !important;
    }
    .fc-daygrid-event-dot {
        display: none !important;
    }

    /* Ajustes finos no FullCalendar para combinar com o layout */
    .fc .fc-toolbar-title {
        font-size: 1.25em !important;
    }
    .fc-theme-standard .fc-scrollgrid {
        border-radius: 8px;
        border: 1px solid var(--psi-border-color);
    }
</style>
@endsection

@section('content')
<div class="container">

    <header class="page-header">
        <h1 id="calendarMainTitle" class="calendar-main-title">
            <i class="bi bi-calendar-month"></i>
            <span>Agosto de 2025</span> </h1>
        <div class="calendar-sub-info">
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
    </header>

    <div class="calendar-controls-bar">
        <div class="control-group">
            @if(auth()->user()?->google_connected)
                <a href="{{ route('google.connect') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-repeat me-1"></i> Reautenticar
                </a>
                <form action="{{ route('google.disconnect') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja desconectar do Google Agenda?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-google-disconnect">
                        <i class="bi bi-x-circle me-1"></i> Desconectar
                    </button>
                </form>
            @else
                <a href="{{ route('google.connect') }}" class="btn btn-sm btn-google-connect">
                    <i class="bi bi-google me-1"></i> Conectar com Google
                </a>
            @endif
        </div>

        <div class="control-group">
            <div class="btn-group" role="group">
                <button id="prevBtn" class="btn btn-sm" title="Anterior"><i class="bi bi-chevron-left"></i></button>
                <button id="todayBtn" class="btn btn-sm">Hoje</button>
                <button id="nextBtn" class="btn btn-sm" title="Próximo"><i class="bi bi-chevron-right"></i></button>
            </div>
            <div class="btn-group" role="group">
                <button id="monthBtn" class="btn btn-sm active">Mês</button>
                <button id="weekBtn" class="btn btn-sm">Semana</button>
                <button id="dayBtn" class="btn btn-sm">Dia</button>
            </div>
        </div>
    </div>


    <div id="calendar-card" class="mt-4">
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
                <select name="paciente_id" id="paciente_id" class="form-select" required>
                    @foreach(\App\Models\Paciente::where('user_id', auth()->id())->get() as $paciente)
                        <option value="{{ $paciente->id }}">{{ $paciente->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small text-muted fw-semibold">Data e Hora</label>
                <input type="datetime-local" name="data_hora" id="data_hora" 
                       class="form-control" required>
            </div>
            <div class="col-6">
                <label class="form-label small text-muted fw-semibold">Valor (R$)</label>
                <input type="number" step="0.01" name="valor" id="valor" 
                       class="form-control" required>
            </div>
            <div class="col-6">
                <label class="form-label small text-muted fw-semibold">Duração (min)</label>
                <input type="number" name="duracao" id="duracao" 
                       class="form-control" value="50" required>
            </div>
            <div class="col-12">
                <div class="form-check mt-2">
                    <input type="checkbox" name="foi_pago" id="foi_pago" class="form-check-input">
                    <label class="form-check-label small fw-semibold" for="foi_pago">Sessão Paga?</label>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/pt-br.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendarTitleEl = document.getElementById('calendarMainTitle').querySelector('span'); // Seleciona o <span> dentro do h1
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

    if (!calendarEl) return;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'pt-br',
        themeSystem: 'bootstrap5',
        timeZone: 'local',
        height: 'auto',
        initialView: 'dayGridMonth',
        headerToolbar: false, // Usaremos nosso header customizado
        events: '/api/sessoes',

        // ATUALIZA O TÍTULO DA PÁGINA
        datesSet: function(info) {
            calendarTitleEl.innerText = info.view.title;
        },

        eventContent: function (arg) {
            const horaInicio = arg.event.start.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            return {
                html: `<div class="fc-event-main-frame">
                           <div class="fc-event-time">${horaInicio}</div>
                           <div class="fc-event-title-container">
                               <div class="fc-event-title fc-sticky">${arg.event.title}</div>
                           </div>
                       </div>`
            };
        },

        dateClick: function (info) {
            abrirModalCriar(info.dateStr);
        },

        eventClick: async function (info) {
            info.jsEvent.preventDefault();
            const id = info.event.id;

            try {
                const res = await fetch(`/sessoes-json/${id}`);
                if (!res.ok) throw new Error('Falha ao carregar dados.');

                const sessao = await res.json();
                
                campos.id.value = sessao.id;
                campos.paciente.value = sessao.paciente_id;
                campos.data_hora.value = sessao.data_hora.slice(0, 16); // Formata para datetime-local
                campos.valor.value = sessao.valor;
                campos.duracao.value = sessao.duracao;
                campos.foi_pago.checked = !!sessao.foi_pago;
                campos.titulo.innerText = "Editar Sessão";

                modal.show();
            } catch(e) {
                Swal.fire('Erro', 'Não foi possível carregar os dados da sessão.', 'error');
            }
        },

    });

    calendar.render();

    // --- CONTROLES CUSTOMIZADOS ---
    const allControlButtons = document.querySelectorAll('.calendar-controls-bar .btn');
    
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
            
            // Gerencia a classe 'active'
            document.querySelectorAll('.control-group .btn-group .btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    // --- LÓGICA DO MODAL ---
    function abrirModalCriar(data) {
        document.getElementById('formSessao').reset(); // Limpa o formulário
        campos.id.value = '';
        campos.data_hora.value = data.length <= 10 ? data + 'T09:00' : data.slice(0, 16);
        campos.duracao.value = 50;
        campos.titulo.innerText = "Nova Sessão";
        modal.show();
    }

    document.getElementById('formSessao').addEventListener('submit', async function (e) {
        e.preventDefault();

        const id = campos.id.value;
        const url = id ? `/sessoes-json/${id}` : '/sessoes-json';
        const method = id ? 'PUT' : 'POST';

        const payload = {
            paciente_id: campos.paciente.value,
            data_hora: campos.data_hora.value,
            valor: campos.valor.value,
            duracao: parseInt(campos.duracao.value),
            foi_pago: campos.foi_pago.checked,
        };

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            const resData = await response.json();

            if (!response.ok) {
                // Trata erro de conflito de horário especificamente
                if (resData?.message?.includes("Conflito de horário")) {
                    Swal.fire('Conflito de Horário', resData.message, 'error');
                } else {
                    Swal.fire('Erro', resData.message || 'Ocorreu um erro ao salvar.', 'error');
                }
                return;
            }
            
            modal.hide();
            calendar.refetchEvents();
            Swal.fire('Sucesso!', 'Sessão salva com sucesso.', 'success');

        } catch (error) {
            Swal.fire('Erro Inesperado', 'Não foi possível completar a operação.', 'error');
        }
    });
});
</script>
@endsection