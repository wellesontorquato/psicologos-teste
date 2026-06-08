<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro interno | PsiGestor</title>

    <style>
        * {
            box-sizing: border-box;
        }

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
            transition: 0.2s;
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
        <img src="{{ asset('images/logo.png') }}" alt="PsiGestor" class="logo">

        <div class="code">500</div>

        <h1>Ops, algo não saiu como esperado</h1>

        <p>
            Tivemos uma instabilidade ao processar sua solicitação.
            Nossa equipe já pode verificar o ocorrido pelos registros internos.
            Tente novamente em alguns instantes.
        </p>

        <a href="{{ url('/') }}" class="btn">Voltar para o início</a>

        <div class="small">
            Seus dados permanecem protegidos. Nenhuma informação técnica é exibida publicamente.
        </div>
    </main>
</body>
</html>