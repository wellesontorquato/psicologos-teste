<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Sessões</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
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
            font-size: 22px;
            color: #00aaff;
        }
        .section {
            padding: 20px 40px;
        }
        h2 {
            font-size: 18px;
            color: #00aaff;
            border-bottom: 1px solid #00aaff;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        p {
            margin: 5px 0;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 30px;
            font-size: 12px;
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
        tfoot td {
            font-weight: bold;
            background: #f9f9f9;
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
        <h2>Resumo</h2>
        <p><strong>Período:</strong> {{ $dataInicial->format('d/m/Y') }} a {{ $dataFinal->format('d/m/Y') }}</p>
        <p><strong>Total de Sessões:</strong> {{ $totais['sessoes'] }}</p>
        <p><strong>Valor Recebido:</strong> R$ {{ number_format($valores['total'], 2, ',', '.') }}</p>

        <h2>Sessões por Mês</h2>
        <table>
            <thead>
                <tr>
                    <th>Mês</th>
                    <th>Total de Sessões</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessaoPorMes as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->mes . '-01')->translatedFormat('F Y') }}</td>
                        <td>{{ $item->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2>Valores Recebidos por Mês</h2>
        <table>
            <thead>
                <tr>
                    <th>Mês</th>
                    <th>Valor Recebido (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($valorPorMes as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->mes . '-01')->translatedFormat('F Y') }}</td>
                        <td>R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Gerado pelo sistema PsiGestor em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
