<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de {{ $paciente->nome }}</title>
    <style>
        @page {
            margin: 30px 40px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #00aaff;
        }

        .header img {
            max-height: 70px;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #00aaff;
        }

        .section {
            padding: 10px 0;
        }

        h3 {
            margin-top: 25px;
            font-size: 14px;
            color: #0d6efd;
        }

        .evento {
            border-left: 4px solid #0d6efd;
            background: #f8f9fa;
            margin: 10px 0;
            padding: 10px 15px;
            border-radius: 6px;
            page-break-inside: avoid;
        }

        .evento h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
        }

        .evento.sessao h4 { color: #198754; }
        .evento.evolucao h4 { color: #0d6efd; }
        .evento.medicacao h4 { color: #dc3545; }

        .data {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .hora {
            color: #666;
            font-size: 11px;
        }

        .descricao {
            font-size: 11.5px;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding: 10px 20px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo-psigestor.png') }}" alt="PsiGestor Logo">
        <h1>Histórico de {{ $paciente->nome }}</h1>
    </div>

    <div class="section">
        @php
            use Illuminate\Support\Str;
            use Carbon\Carbon;
            $agrupadosPorData = collect($eventos)->groupBy('data');
        @endphp

        @forelse ($agrupadosPorData as $data => $grupo)
            <h3>{{ Carbon::parse($data)->format('d/m/Y') }}</h3>

            @foreach ($grupo as $evento)
                @php
                    $classeTipo = match($evento['tipo']) {
                        'Sessão' => 'sessao',
                        'Evolução' => 'evolucao',
                        'Medicação' => 'medicacao',
                        default => ''
                    };

                    $titulo = match($evento['tipo']) {
                        'Sessão' => 'Sessão',
                        'Evolução' => 'Evolução',
                        'Medicação' => Str::startsWith($evento['descricao'], 'Medicação Inicial:') ? 'Medicação Inicial' : 'Nova Medicação',
                        default => $evento['tipo']
                    };

                    $status = Str::upper(trim($evento['status_confirmacao'] ?? ''));
                    $isSessaoConfirmada = $evento['tipo'] === 'Sessão' && $status === 'CONFIRMADA';
                @endphp

                @if ($evento['tipo'] === 'Sessão' && !$isSessaoConfirmada)
                    @continue
                @endif

                <div class="evento {{ $classeTipo }}">
                    <h4>{{ $titulo }}</h4>
                    <div class="data">
                        {{ $evento['data'] }}
                        @if($evento['hora'])
                            às <span class="hora">{{ $evento['hora'] }}</span>
                        @endif
                    </div>
                    <div class="descricao">
                        {!! strip_tags($evento['descricao'], '<br><b><strong><em><i><u>') !!}
                    </div>
                </div>
            @endforeach
        @empty
            <p style="margin-top: 30px;">Nenhum evento registrado.</p>
        @endforelse
    </div>

    {{-- Paginação no rodapé do PDF --}}
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                if ($PAGE_COUNT > 1) {
                    $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                    $size = 9;
                    $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;
                    $x = 520;
                    $y = 820;
                    $pdf->text($x, $y, $pageText, $font, $size);
                }
            ');
        }
    </script>

    <div class="footer">
        Histórico gerado pelo sistema PsiGestor em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
