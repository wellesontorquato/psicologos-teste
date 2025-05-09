<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\AuditHelper;

class ArquivoController extends Controller
{
    use AuthorizesRequests;

    /**
     * Exibe todos os arquivos de um paciente.
     */
    public function index(Paciente $paciente)
    {
        $this->authorize('view', $paciente);

        $arquivos = $paciente->arquivos()->latest()->get();

        AuditHelper::log('view_arquivos', 'Visualizou os arquivos do paciente ' . $paciente->nome);

        return view('arquivos.index', compact('paciente', 'arquivos'));
    }

    /**
     * Salva um novo arquivo para o paciente (upload para S3).
     */
    public function store(Request $request, Paciente $paciente)
    {
        $this->authorize('update', $paciente);

        $request->validate([
            'arquivo' => 'required|file|max:5120', // 5MB máx
        ]);

        $file = $request->file('arquivo');

        // ✅ Define o caminho com subpasta por paciente (opcional)
        $caminho = 'arquivos/' . $paciente->id . '/' . uniqid() . '_' . $file->getClientOriginalName();

        // ✅ Salva no S3
        Storage::disk('s3')->put($caminho, file_get_contents($file), 'public');

        $arquivo = Arquivo::create([
            'paciente_id' => $paciente->id,
            'nome' => $file->getClientOriginalName(),
            'caminho' => $caminho, // salva só o caminho relativo
        ]);

        AuditHelper::log('uploaded_file', 'Enviou o arquivo "' . $arquivo->nome . '" para o paciente ' . $paciente->nome);

        return redirect()->back()->with('success', 'Arquivo enviado com sucesso!');
    }

    /**
     * Renomeia um arquivo (apenas no banco, não muda o S3).
     */
    public function renomear(Request $request, Arquivo $arquivo)
    {
        $this->authorize('update', $arquivo);

        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $antigo = $arquivo->nome;
        $arquivo->update(['nome' => $request->nome]);

        AuditHelper::log('renamed_file', 'Renomeou o arquivo "' . $antigo . '" para "' . $arquivo->nome . '"');

        return back()->with('success', 'Nome do arquivo atualizado!');
    }

    /**
     * Remove um arquivo (apaga no S3 e no banco).
     */
    public function destroy(Arquivo $arquivo)
    {
        $this->authorize('delete', $arquivo);

        // ✅ Remove no S3 se existir
        if (Storage::disk('s3')->exists($arquivo->caminho)) {
            Storage::disk('s3')->delete($arquivo->caminho);
        }

        $nome = $arquivo->nome;
        $paciente = $arquivo->paciente->nome ?? 'Desconhecido';

        $arquivo->delete();

        AuditHelper::log('deleted_file', 'Removeu o arquivo "' . $nome . '" do paciente ' . $paciente);

        return back()->with('success', 'Arquivo excluído com sucesso.');
    }
}
