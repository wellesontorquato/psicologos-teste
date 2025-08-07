<table>
    <thead>
        <tr>
            <th>Arquivo</th>
            <th>Última modificação</th>
            <th>Tamanho (bytes)</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($files as $file)
            <tr>
                <td>{{ $file['path'] }}</td>
                <td>{{ $file['last_modified'] }}</td>
                <td>{{ $file['size'] }}</td>
                <td>
                    <a href="{{ route('filesystem.view', ['file' => $file['path']]) }}" target="_blank">Visualizar</a> |
                    <a href="{{ route('filesystem.download', ['file' => $file['path']]) }}">Baixar</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
