<!-- resources/views/sessoes/export-pdf.blade.php -->

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Sessões</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Relatório de Sessões</h2>

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
</body>
</html>
