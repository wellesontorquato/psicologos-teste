<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Contato</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            padding: 20px;
        }
        .header {
            background-color: #00aaff;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .logo {
            max-width: 180px;
            margin: 0 auto 15px auto;
            display: block;
        }
        .content {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .content p {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ versao('images/logo-psigestor-branca.png') }}" alt="PsiGestor Logo" class="logo">
        <h2>Novo contato via PsiGestor</h2>
    </div>

    <div class="content">
        <p><span class="label">Nome:</span> {{ $dados['nome'] }}</p>
        <p><span class="label">E-mail:</span> {{ $dados['email'] }}</p>
        <p><span class="label">Telefone:</span> {{ $dados['telefone'] ?: 'Não informado' }}</p>
        <p><span class="label">Assunto:</span> {{ $dados['assunto'] }}</p>
        <p><span class="label">Mensagem:</span></p>
        <p>{{ $dados['mensagem'] }}</p>
    </div>

</body>
</html>
