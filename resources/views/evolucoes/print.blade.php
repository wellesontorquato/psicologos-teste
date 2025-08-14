<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Evolução de {{ $evolucao->paciente->nome }}</title>
    <style>
        @page { margin: 30px 40px; }

        body{
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color:#333; margin:0; padding:0;
            background:#fff;
        }

        .header{
            text-align:center;
            margin-bottom:20px;
            padding-bottom:10px;
            border-bottom:3px solid #00aaff;
        }
        .header img{ max-height:70px; margin-bottom:10px; }
        .header h1{ margin:0; font-size:20px; color:#00aaff; }
        .header .sub{ margin-top:6px; color:#777; font-size:11px; }

        .section{ padding: 10px 0; }

        .meta{
            display:grid;
            grid-template-columns: repeat(2,minmax(0,1fr));
            gap:10px 16px;
            margin-top:6px;
        }
        .meta .row{ display:flex; gap:6px; }
        .meta .label{ min-width:120px; color:#666; font-weight:bold; }
        .meta .val{ color:#111; }

        .evento{
            border-left:4px solid #0d6efd;
            background:#f8f9fa;
            margin:14px 0;
            padding:12px 15px;
            border-radius:6px;
            page-break-inside: avoid;
        }
        .evento.evolucao h4{ color:#0d6efd; }
        .evento h4{ margin:0 0 6px 0; font-size:14px; }
        .data{ font-weight:bold; margin-bottom:3px; }
        .hora{ color:#666; font-size:11px; }
        .descricao{
            font-size:11.5px; margin-top:6px; line-height:1.55;
            white-space: pre-wrap; /* preserva quebras de linha do texto */
        }

        .footer{
            text-align:center; font-size:10px; color:#999;
            border-top:1px solid #eee; padding:10px 20px;
            position:fixed; bottom:0; left:0; right:0; background:#fff;
        }

        /* esconder a “toolbar” (apenas na tela) quando imprimir */
        @media print { .toolbar{ display:none !important; } }
        .toolbar{
            display:flex; gap:8px; justify-content:flex-end;
            margin: 12px 0 6px;
        }
        .btn{
            border:1px solid #e6eaef; background:#fff; color:#0b7f83;
            padding:8px 12px; border-radius:8px; font-weight:700; cursor:pointer;
        }
        .btn-primary{ background:#00aaff; color:#fff; border-color:#0093e0; }
    </style>
</head>
<body>
@php
    use Illuminate\Support\Carbon;
    // logo: usa public_path se existir (ideal p/ DomPDF); senão, asset (ideal p/ navegador)
    $logoPublic = public_path('images/logo-psigestor.png');
    $logoSrc    = file_exists($logoPublic) ? $logoPublic : asset('images/logo-psigestor.png');

    $dataEvo = \Illuminate\Support\Carbon::parse($evolucao->data);
    $sessao  = $evolucao->sessao;
    // sanitiza mantendo tags simples
    $texto   = strip_tags($evolucao->texto, '<b><strong><em><i><u><br>');
@endphp

<div class="toolbar">
    <button class="btn" onclick="window.close()">Fechar</button>
    <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
</div>

<div class="header">
    <img src="{{ $logoSrc }}" alt="PsiGestor Logo">
    <h1>Evolução de {{ $evolucao->paciente->nome }}</h1>
    <div class="sub">{{ config('app.name') }} • gerado em {{ now()->format('d/m/Y H:i') }}</div>
</div>

<div class="section">
    <div class="meta">
        <div class="row">
            <div class="label">Paciente</div>
            <div class="val">{{ $evolucao->paciente->nome }}</div>
        </div>
        <div class="row">
            <div class="label">Profissional</div>
            <div class="val">{{ $user->name }}</div>
        </div>
        <div class="row">
            <div class="label">Data da evolução</div>
            <div class="val">{{ $dataEvo->format('d/m/Y') }}</div>
        </div>
        <div class="row">
            <div class="label">Sessão</div>
            <div class="val">
                @if($sessao)
                    {{ Carbon::parse($sessao->data_hora)->format('d/m/Y H:i') }} ({{ (int)$sessao->duracao }}min)
                @else
                    — Sem vínculo
                @endif
            </div>
        </div>
    </div>

    <div class="evento evolucao">
        <h4>Evolução</h4>
        <div class="data">
            {{ $dataEvo->format('d/m/Y') }}
            @if($sessao)
                às <span class="hora">{{ Carbon::parse($sessao->data_hora)->format('H:i') }}</span>
            @endif
        </div>
        <div class="descricao">{!! $texto !!}</div>
    </div>
</div>

{{-- paginação quando renderizado via DomPDF --}}
<script type="text/php">
@if(isset($pdf))
    $pdf->page_script('
        if ($PAGE_COUNT > 1) {
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 9;
            $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;
            $x = 520; $y = 820;
            $pdf->text($x, $y, $pageText, $font, $size);
        }
    ');
@endif
</script>

<div class="footer">
    Impressão gerada pelo sistema {{ config('app.name') }} em {{ now()->format('d/m/Y H:i') }}
</div>

<script>
  // imprime automaticamente quando abrir em nova aba
  window.addEventListener('load', () => setTimeout(() => window.print(), 50));
</script>
</body>
</html>
