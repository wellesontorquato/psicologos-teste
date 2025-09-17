<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\AuditHelper;
use Illuminate\Support\Str;

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
     * Salva um novo arquivo para o paciente (upload para S3/Contabo).
     */
    public function store(Request $request, Paciente $paciente)
    {
        $this->authorize('update', $paciente);

        $request->validate([
            'arquivo' => 'required|file|max:5120', // 5MB
        ]);

        $file = $request->file('arquivo');

        // Pasta por paciente
        $dir = 'arquivos/' . $paciente->id;

        // Nome de arquivo seguro: slug do nome + extensão, com prefixo único
        $origName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext      = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $safeName = Str::slug($origName, '-') ?: 'arquivo';
        $filename = uniqid('', true) . '_' . $safeName . '.' . $ext;

        // Upload público com headers adequados (MIME + cache)
        // Use putFileAs para evitar carregar o arquivo na memória (stream)
        $path = Storage::disk('s3')->putFileAs(
            $dir,
            $file,
            $filename,
            [
                'visibility'   => 'public',
                'CacheControl' => 'public, max-age=31536000, immutable',
                'ContentType'  => $file->getMimeType(), // garante exibição correta
            ]
        );

        $arquivo = Arquivo::create([
            'paciente_id' => $paciente->id,
            'nome'        => $file->getClientOriginalName(), // mantém o nome "humano"
            'caminho'     => $path,                           // salva caminho relativo
        ]);

        AuditHelper::log('uploaded_file', 'Enviou o arquivo "' . $arquivo->nome . '" para o paciente ' . $paciente->nome);

        return back()->with('success', 'Arquivo enviado com sucesso!');
    }

    /**
     * Renomeia um arquivo (apenas no banco, não muda no S3).
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

        // Remove no S3 (idempotente e silencioso em caso de erro)
        try {
            if (Storage::disk('s3')->exists($arquivo->caminho)) {
                Storage::disk('s3')->delete($arquivo->caminho);
            }
        } catch (\Throwable $e) {
            // opcional: logar $e->getMessage()
        }

        $nome     = $arquivo->nome;
        $paciente = $arquivo->paciente->nome ?? 'Desconhecido';

        $arquivo->delete();

        AuditHelper::log('deleted_file', 'Removeu o arquivo "' . $nome . '" do paciente ' . $paciente);

        return back()->with('success', 'Arquivo excluído com sucesso.');
    }
}
