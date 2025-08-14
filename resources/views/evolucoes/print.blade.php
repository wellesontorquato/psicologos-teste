<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Evolução de {{ $evolucao->paciente->nome }}</title>
    <style>
        /* Margens da página no PDF */
        @page { margin: 28px 36px; }

        /* Base */
        body{
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12.5px;
            color:#222; margin:0; padding:0; background:#fff;
            line-height: 1.55;
        }
        h1,h2,h3,h4{ margin:0; }
        .muted{ color:#666; }
        .bold{ font-weight:700; }

        /* Header */
        .header{
            text-align:center;
            padding: 4px 0 12px 0;
            margin-bottom: 10px;
            border-bottom: 2px solid #0aa9ff;
        }
        .logo{
            max-height: 64px;
            display: inline-block;
            margin: 2px auto 6px auto;
        }
        .title{
            font-size: 21px;
            color:#0aa9ff;
            font-weight: 800;
            margin-bottom: 2px;
        }
        .subtitle{
            font-size: 11px; color:#777;
        }
        .title-band{
            height: 4px;
            background: #0aa9ff;
            margin-top: 10px;
            border-radius: 2px;
        }

        /* Toolbar (somente ao abrir no navegador) */
        .toolbar{
            text-align:right;
            margin: 10px 0 0 0;
        }
        .btn{
            display:inline-block;
            border:1px solid #e5e8ef;
            background:#fff;
            color:#0b7f83;
            padding:7px 12px;
            border-radius:8px;
            font-weight:700;
            cursor:pointer;
            margin-left:6px;
        }
        .btn-primary{ background:#0aa9ff; color:#fff; border-color:#0a90da; }

        /* Info em tabela – mais estável no DomPDF */
        .info{
            width:100%;
            border-collapse: collapse;
            margin: 12px 0 14px 0;
            table-layout: fixed;
        }
        .info th, .info td{
            text-align:left;
            padding: 6px 8px;
            vertical-align: top;
        }
        .info th{
            width: 160px;
            color:#555;
            font-weight:700;
            background:#f6fafe;
            border:1px solid #e9eef6;
        }
        .info td{
            border:1px solid #e9eef6;
            background:#fff;
        }

        /* Card da evolução */
        .card{
            border:1px solid #e9eef6;
            background:#f9fbff;
            border-left: 4px solid #0d6efd;
            border-radius: 6px;
            padding: 10px 12px;
            page-break-inside: avoid;
        }
        .card h4{
            font-size: 14px;
            color:#0d6efd;
            margin-bottom: 6px;
        }
        .meta-line{
            font-size: 12px;
            margin-bottom: 6px;
        }
        .meta-line .time{ color:#666; }
        .content{
            font-size: 12.5px;
            color:#222;
            word-wrap: break-word;
            white-space: pre-wrap; /* respeita quebras de linha digitadas */
        }

        /* Rodapé fixo */
        .footer{
            position: fixed;
            bottom: 0; left: 0; right: 0;
            text-align:center;
            font-size:10px;
            color:#999;
            border-top:1px solid #eee;
            padding:8px 10px;
            background: #fff;
        }

        /* Impressão: esconde toolbar */
        @media print { .toolbar{ display:none !important; } }
    </style>
</head>
<body>
@php
    use Illuminate\Support\Carbon;

    // logo: public_path (PDF) ou asset (navegador)
    $logoPublic = public_path('images/logo-psigestor.png');
    $logoSrc    = file_exists($logoPublic) ? $logoPublic : asset('images/logo-psigestor.png');

    $dataEvo = Carbon::parse($evolucao->data);
    $sessao  = $evolucao->sessao;

    // Permite apenas tags básicas
    $texto   = strip_tags($evolucao->texto, '<b><strong><em><i><u><br>');
@endphp

{{-- Toolbar só para navegação web --}}
<div class="toolbar">
    <button class="btn" onclick="window.close()">Fechar</button>
    <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
</div>

<!-- Cabeçalho -->
<div class="header">
    <img class="logo" src="{{ $logoSrc }}" alt="PsiGestor Logo">
    <div class="title">Evolução de {{ $evolucao->paciente->nome }}</div>
    <div class="subtitle">{{ config('app.name') }} • gerado em {{ now()->format('d/m/Y H:i') }}</div>
    <div class="title-band"></div>
</div>

<!-- Metadados -->
<table class="info">
    <tr>
        <th>Paciente</th>
        <td>{{ $evolucao->paciente->nome }}</td>
        <th>Profissional</th>
        <td>{{ $user->name }}</td>
    </tr>
    <tr>
        <th>Data da evolução</th>
        <td>{{ $dataEvo->format('d/m/Y') }}</td>
        <th>Sessão</th>
        <td>
            @if($sessao)
                {{ Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }}
                ({{ (int)$sessao->duracao }} min)
            @else
                — Sem vínculo
            @endif
        </td>
    </tr>
</table>

<!-- Conteúdo da evolução -->
<div class="card">
    <h4>Evolução</h4>
    <div class="meta-line">
        <span class="bold">{{ $dataEvo->format('d/m/Y') }}</span>
        @if($sessao)
            <span class="time">às {{ Carbon::parse($sessao->data_hora)->format('H:i') }}</span>
        @endif
    </div>
    <div class="content">{!! $texto !!}</div>
</div>

{{-- Paginação quando renderizado via DomPDF --}}
<script type="text/php">
@if (isset($pdf))
    $pdf->page_script('
        if ($PAGE_COUNT > 1) {
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 9;
            $text = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;
            $x = 520; $y = 820;
            $pdf->text($x, $y, $text, $font, $size);
        }
    ');
@endif
</script>

<div class="footer">
    Impressão gerada por {{ config('app.name') }} em {{ now()->format('d/m/Y H:i') }}
</div>

<script>
  // imprime automaticamente quando aberto no navegador
  window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 60); });
</script>
</body>
</html>
