<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Sessões</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11.5px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            padding: 20px;
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
            padding: 20px 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #eaf9ff;
            color: #007799;
            font-weight: bold;
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
        <h1>Relatório de Sessões</h1>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Data</th>
                    <th>Duração</th>
                    <th>Valor</th>
                    <th>Pago?</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessoes as $sessao)
                    @php
                        $status = $sessao->status_confirmacao ?? 'PENDENTE';

                        if ($status === 'REMARCAR' && is_null($sessao->data_hora)) {
                            $statusFormatado = 'Reagendar Consulta';
                        } elseif ($status === 'REMARCAR' && !is_null($sessao->data_hora)) {
                            $statusFormatado = 'Remarcado';
                        } elseif ($status === 'CANCELADA') {
                            $statusFormatado = 'Cancelada';
                        } else {
                            $statusFormatado = ucfirst(strtolower($status));
                        }

                        $dataFormatada = is_null($sessao->data_hora)
                            ? ($status === 'REMARCAR' ? 'Reagendar Consulta' : ($status === 'CANCELADA' ? 'Consulta Cancelada' : '—'))
                            : \Carbon\Carbon::parse($sessao->data_hora)->format('d/m/Y H:i');
                    @endphp

                    <tr>
                        <td>{{ $sessao->paciente->nome }}</td>
                        <td>{{ $dataFormatada }}</td>
                        <td>{{ $sessao->duracao }} min</td>
                        <td>R$ {{ number_format($sessao->valor, 2, ',', '.') }}</td>
                        <td>{{ $sessao->foi_pago ? 'Sim' : 'Não' }}</td>
                        <td>{{ $statusFormatado }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
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
        Relatório gerado pelo sistema PsiGestor em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
