<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Explorador de Arquivos</title>
</head>
<body>
    <h1>Arquivos em {{ public_path() }}</h1>
    <ul>
        @foreach ($files as $file)
            <li>{{ $file }}</li>
        @endforeach
    </ul>
</body>
</html>
