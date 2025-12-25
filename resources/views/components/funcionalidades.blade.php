<section class="section-features" style="background-color: #ffffff; padding: 60px 20px;">
    <div style="max-width: 1200px; margin: auto;">
        <h2 style="text-align: center; font-size: 1.8rem; margin-bottom: 20px; font-weight: bold;">
            Funcionalidades do PsiGestor
        </h2>
        <p style="text-align: center; color: #666; max-width: 700px; margin: 0 auto 40px;">
            Descubra os recursos que tornam sua clínica mais eficiente, organizada e conectada.
        </p>

        <div class="features-grid" style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
        ">
            @php
                $features = [
                    [
                        'icon' => 'calendar-check',
                        'title' => 'Agenda Visual',
                        'desc'  => 'Drag & drop e visualização por dia ou semana.',
                        'details_title' => 'Agenda Visual + Google Agenda + Google Meet',
                        'details' => 'Sincronização com o Google Agenda (com acesso às agendas já existentes) e envio automático de e-mails ao paciente com o link da sala do Google Meet ao criar o atendimento.'
                    ],
                    [
                        'icon' => 'file-lines',
                        'title' => 'Evoluções',
                        'desc'  => 'Linha do tempo com registros detalhados de cada sessão.',
                        'details_title' => null,
                        'details' => null
                    ],
                    [
                        'icon' => 'coins',
                        'title' => 'Financeiro',
                        'desc'  => 'Controle de pagamentos e notificações de sessões não pagas.',
                        'details_title' => 'Financeiro com Multimoedas',
                        'details' => 'Suporte a multimoedas para organizar atendimentos e recebimentos em diferentes moedas.'
                    ],
                    [
                        'icon' => 'cloud-upload-alt',
                        'title' => 'Arquivos',
                        'desc'  => 'Documentos organizados por tipo: exames, contratos e relatórios.',
                        'details_title' => null,
                        'details' => null
                    ],
                    [
                        'icon' => 'whatsapp',
                        'title' => 'Confirmação por WhatsApp',
                        'desc'  => 'Envio automático de lembretes e confirmações de sessão.',
                        'details_title' => null,
                        'details' => null
                    ],
                    [
                        'icon' => 'chart-line',
                        'title' => 'Painel de Indicadores',
                        'desc'  => 'Gráficos com estatísticas por período e evolução da clínica.',
                        'details_title' => null,
                        'details' => null
                    ],
                    [
                        'icon' => 'user-shield',
                        'title' => 'Banco de dados robusto',
                        'desc'  => 'Seus dados protegidos com segurança e backups automáticos.',
                        'details_title' => null,
                        'details' => null
                    ],
                    [
                        'icon' => 'file-export',
                        'title' => 'Exportações em PDF e Excel',
                        'desc'  => 'Relatórios com filtros por data, status e pagamento.',
                        'details_title' => null,
                        'details' => null
                    ],
                ];
            @endphp

            @foreach ($features as $f)
                <div class="feature-card">
                    <i class="fa-solid fa-{{ $f['icon'] }} feature-icon"></i>

                    <h4 class="feature-title">{{ $f['title'] }}</h4>

                    <p class="feature-desc">{{ $f['desc'] }}</p>

                    @if(!empty($f['details']))
                        <button
                            type="button"
                            class="feature-more-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#featureModal"
                            data-feature-title="{{ $f['details_title'] ?? $f['title'] }}"
                            data-feature-body="{{ $f['details'] }}"
                        >
                            Ver detalhes
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Modal Único (padrão sites grandes) --}}
    <div class="modal fade" id="featureModal" tabindex="-1" aria-labelledby="featureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(to right, #00aaff, #00c4ff);">
                    <h5 class="modal-title" id="featureModalLabel" style="color: #fff; font-weight: 800; margin: 0;">
                        Detalhes
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body" style="padding: 22px;">
                    <p id="featureModalBody" style="margin: 0; color: #333; line-height: 1.6;"></p>
                </div>

                <div class="modal-footer" style="border-top: 1px solid #eee;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .feature-card {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 3px 8px rgba(0,0,0,0.05);
        transition: 0.25s ease;
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 220px;
    }

    .feature-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.08);
    }

    .feature-icon {
        font-size: 1.8rem;
        color: #00aaff;
        margin-bottom: 5px;
    }

    .feature-title {
        font-size: 1.1rem;
        margin: 0;
        font-weight: 800;
        color: #111;
    }

    .feature-desc {
        font-size: 0.95rem;
        color: #555;
        line-height: 1.45;

        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 0;
    }

    .feature-more-btn {
        margin-top: auto;
        align-self: center;

        border: 1px solid rgba(0,170,255,0.35);
        background: rgba(0,170,255,0.08);
        color: #008ecc;
        font-weight: 800;
        border-radius: 999px;
        padding: 8px 14px;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .feature-more-btn:hover {
        background: rgba(0,170,255,0.14);
        border-color: rgba(0,170,255,0.55);
        transform: translateY(-1px);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('featureModal');
        if (!modalEl) return;

        modalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const title = button?.getAttribute('data-feature-title') || 'Detalhes';
            const body  = button?.getAttribute('data-feature-body') || '';

            const titleEl = modalEl.querySelector('#featureModalLabel');
            const bodyEl  = modalEl.querySelector('#featureModalBody');

            if (titleEl) titleEl.textContent = title;
            if (bodyEl) bodyEl.textContent = body;
        });
    });
</script>
