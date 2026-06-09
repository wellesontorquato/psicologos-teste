<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviço indisponível | PsiGestor</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #eef9ff;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .container {
            width: 100%;
            max-width: 540px;
            background: #ffffff;
            border-radius: 22px;
            padding: 42px 34px;
            text-align: center;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        .logo {
            max-width: 190px;
            margin-bottom: 28px;
        }

        .code {
            font-size: 78px;
            font-weight: 800;
            color: #0ea5e9;
            line-height: 1;
            margin-bottom: 16px;
        }

        h1 {
            font-size: 28px;
            margin: 0 0 14px;
            color: #020617;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            color: #475569;
            margin: 0 0 28px;
        }

        .btn {
            display: inline-block;
            background: #0ea5e9;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 24px;
            border-radius: 10px;
            font-weight: 700;
        }

        .btn:hover {
            background: #0284c7;
        }

        .small {
            margin-top: 20px;
            font-size: 13px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <main class="container">
        <img src="{{ asset('images/logo-psigestor.png') }}" alt="PsiGestor" class="logo">

        <div class="code">503</div>

        <h1>Serviço temporariamente indisponível</h1>

        <p>
            O PsiGestor está passando por uma manutenção ou instabilidade momentânea.
            Tente acessar novamente em alguns instantes.
        </p>

        <a href="{{ url('/') }}" class="btn">Tentar novamente</a>

        <div class="small">
            Estamos trabalhando para normalizar o acesso o mais rápido possível.
        </div>
    </main>
</body>
</html>