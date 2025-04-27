<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de {{ $paciente->nome }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .evento {
            border-left: 3px solid #0d6efd;
            margin-bottom: 20px;
            padding-left: 10px;
        }

        .evento h4 {
            margin: 0;
            font-size: 14px;
        }

        .evento.sessao h4 { color: #198754; }
        .evento.evolucao h4 { color: #0d6efd; }
        .evento.medicacao h4 { color: #dc3545; }

        .data {
            font-weight: bold;
        }

        .hora {
            color: #555;
        }

        .descricao {
            margin-top: 5px;
            white-space: pre-wrap;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            text-align: center;
            font-size: 10px;
            width: 100%;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <h2>Histórico de {{ $paciente->nome }}</h2>

    @forelse ($eventos as $evento)
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
                'Medicação' => Str::startsWith($evento['descricao'], 'Medicação Inicial:')
                    ? 'Medicação Inicial'
                    : 'Nova Medicação',
                default => $evento['tipo']
            };
        @endphp

        <div class="evento {{ $classeTipo }}">
            <h4>{{ $titulo }}</h4>
            <div class="data">
                {{ $evento['data'] }}
                @if($evento['hora'])
                    às <span class="hora">{{ $evento['hora'] }}</span>
                @endif
            </div>
            <div class="descricao">{!! strip_tags($evento['descricao'], '<br><b><strong><em><i><u>') !!}</div>
        </div>
    @empty
        <p>Nenhum evento registrado.</p>
    @endforelse

    <div class="footer">
        Histórico gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
