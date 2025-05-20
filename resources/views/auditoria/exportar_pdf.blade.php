<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Auditoria</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
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
            font-size: 10.5px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
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
        <h1>Relatório de Auditoria</h1>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Descrição</th>
                    <th>IP</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registros as $log)
                    <tr>
                        <td>{{ optional($log->user)->name ?? 'Desconhecido' }}</td>
                        <td>{{ $log->action ?? '-' }}</td>
                        <td>{{ $log->description ?? '-' }}</td>
                        <td>{{ $log->ip_address ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Rodapé com paginação --}}
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
        Gerado pelo sistema PsiGestor em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
