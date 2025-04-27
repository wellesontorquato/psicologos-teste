<table>
    <thead>
        <tr>
            <th colspan="2">Relatório de Sessões</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Período</td>
            <td>{{ $dataInicial->format('d/m/Y') }} a {{ $dataFinal->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Total de Sessões</td>
            <td>{{ $totais['sessoes'] }}</td>
        </tr>
        <tr>
            <td>Valor Recebido</td>
            <td>R$ {{ number_format($valores['total'], 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<br><br>

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
                <td>{{ \Carbon\Carbon::parse($item->mes . '-01')->format('m/Y') }}</td>
                <td>{{ $item->total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<br><br>

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
                <td>{{ \Carbon\Carbon::parse($item->mes . '-01')->format('m/Y') }}</td>
                <td>{{ number_format($item->total, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
