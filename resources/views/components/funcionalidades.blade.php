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
                    ['icon' => 'calendar-check', 'title' => 'Agenda Visual', 'desc' => 'Drag & drop, notificações, e visualização por semana ou dia.'],
                    ['icon' => 'file-lines', 'title' => 'Evoluções', 'desc' => 'Linha do tempo com registros detalhados de cada sessão.'],
                    ['icon' => 'coins', 'title' => 'Financeiro', 'desc' => 'Controle de pagamentos, envio de notificações de sessões anteriores não pagas.'],
                    ['icon' => 'cloud-upload-alt', 'title' => 'Arquivos', 'desc' => 'Documentos organizados por tipo: exames, contratos, relatórios.'],
                    ['icon' => 'whatsapp', 'title' => 'Confirmação por WhatsApp', 'desc' => 'Envio automático de lembretes e confirmações de sessão.'],
                    ['icon' => 'chart-line', 'title' => 'Painel de Indicadores', 'desc' => 'Gráficos com estatísticas por período e evolução da clínica.'],
                    ['icon' => 'user-shield', 'title' => 'Banco de dados robusto', 'desc' => 'Não se preocupe, conosco seus arquivos estarão sempre bem guardados.'],
                    ['icon' => 'file-export', 'title' => 'Exportações em PDF e Excel', 'desc' => 'Relatórios completos com filtros por data, status e pagamento.'],
                ];
            @endphp

            @foreach ($features as $f)
                <div style="background: #f8f9fa; border-radius: 16px; padding: 25px; text-align: center; box-shadow: 0 3px 8px rgba(0,0,0,0.05); transition: 0.3s ease;">
                    <i class="fa-solid fa-{{ $f['icon'] }}" style="font-size: 1.8rem; color: #00aaff; margin-bottom: 15px;"></i>
                    <h4 style="font-size: 1.1rem; margin-bottom: 10px;">{{ $f['title'] }}</h4>
                    <p style="font-size: 0.95rem; color: #555;">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
