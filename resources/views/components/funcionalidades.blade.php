<section class="section-features" style="background-color: #ffffff; padding: 60px 20px;">
    <div style="max-width: 1200px; margin: auto;">
        <h2 style="text-align: center; font-size: 1.8rem; margin-bottom: 20px; font-weight: bold;">
            Funcionalidades do PsiGestor
        </h2>
        <p style="text-align: center; color: #666; max-width: 700px; margin: 0 auto 40px;">
            Descubra os recursos que tornam sua clínica mais eficiente, organizada e conectada.
        </p>

        <div style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
        ">
            @php
                $features = [
                    [
                        'icon' => 'calendar-check',
                        'title' => 'Agenda Visual',
                        'desc'  => 'Agenda com drag & drop, visualização por dia ou semana, sincronização com o Google Agenda (visualizando agendas já existentes) e envio automático de e-mails ao paciente com o link da sala do Google Meet ao criar o atendimento.'
                    ],
                    [
                        'icon' => 'file-lines',
                        'title' => 'Evoluções',
                        'desc'  => 'Linha do tempo completa com registros clínicos organizados e histórico detalhado de cada sessão.'
                    ],
                    [
                        'icon' => 'coins',
                        'title' => 'Financeiro',
                        'desc'  => 'Controle de pagamentos, notificações de sessões não pagas e suporte a multimoedas para atendimentos e recebimentos em diferentes moedas.'
                    ],
                    [
                        'icon' => 'cloud-upload-alt',
                        'title' => 'Arquivos',
                        'desc'  => 'Armazene e organize documentos por tipo, como exames, contratos, relatórios e anexos clínicos.'
                    ],
                    [
                        'icon' => 'whatsapp',
                        'title' => 'Confirmação por WhatsApp',
                        'desc'  => 'Envio automático de lembretes e confirmações de sessão diretamente para o WhatsApp do paciente.'
                    ],
                    [
                        'icon' => 'chart-line',
                        'title' => 'Painel de Indicadores',
                        'desc'  => 'Visualize gráficos e estatísticas por período, acompanhando a evolução da sua clínica.'
                    ],
                    [
                        'icon' => 'user-shield',
                        'title' => 'Banco de dados robusto',
                        'desc'  => 'Seus dados protegidos com segurança, estabilidade e backups automáticos.'
                    ],
                    [
                        'icon' => 'file-export',
                        'title' => 'Exportações em PDF e Excel',
                        'desc'  => 'Gere relatórios completos com filtros por data, status de sessão e situação de pagamento.'
                    ],
                ];
            @endphp

            @foreach ($features as $f)
                <div style="
                    background: #f8f9fa;
                    border-radius: 16px;
                    padding: 25px;
                    text-align: center;
                    box-shadow: 0 3px 8px rgba(0,0,0,0.05);
                    transition: 0.3s ease;
                ">
                    <i class="fa-solid fa-{{ $f['icon'] }}" style="font-size: 1.8rem; color: #00aaff; margin-bottom: 15px;"></i>
                    <h4 style="font-size: 1.1rem; margin-bottom: 10px;">
                        {{ $f['title'] }}
                    </h4>
                    <p style="font-size: 0.95rem; color: #555;">
                        {{ $f['desc'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
