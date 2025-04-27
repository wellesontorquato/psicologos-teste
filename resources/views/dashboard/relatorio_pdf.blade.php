<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Sessões</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2, h4 { margin-bottom: 5px; }
    </style>
</head>
<body>
    <h2>Relatório de Sessões</h2>
    <p><strong>Período:</strong> {{ $dataInicial->format('d/m/Y') }} a {{ $dataFinal->format('d/m/Y') }}</p>
    <p><strong>Total de Sessões:</strong> {{ $totais['sessoes'] }}</p>
    <p><strong>Valor Recebido:</strong> R$ {{ number_format($valores['total'], 2, ',', '.') }}</p>

    <h4>Sessões por Mês</h4>
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

    <h4 style="margin-top: 30px;">Valores Recebidos por Mês</h4>
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
</body>
</html>
