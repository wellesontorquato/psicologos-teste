<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\AuditHelper; // ✅ Importa o helper

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

        AuditHelper::log('view_arquivos', 'Visualizou os arquivos do paciente ' . $paciente->nome); // ✅ Log de visualização

        return view('arquivos.index', compact('paciente', 'arquivos'));
    }

    /**
     * Salva um novo arquivo para o paciente.
     */
    public function store(Request $request, Paciente $paciente)
    {
        $this->authorize('update', $paciente);

        $request->validate([
            'arquivo' => 'required|file|max:5120', // 5MB
        ]);

        $file = $request->file('arquivo');
        $path = $file->store('arquivos', 'public');

        $arquivo = Arquivo::create([
            'paciente_id' => $paciente->id,
            'nome' => $file->getClientOriginalName(),
            'caminho' => $path,
        ]);

        AuditHelper::log('uploaded_file', 'Enviou o arquivo "' . $arquivo->nome . '" para o paciente ' . $paciente->nome); // ✅ Log de envio

        return redirect()->back()->with('success', 'Arquivo enviado com sucesso!');
    }

    /**
     * Renomeia um arquivo.
     */
    public function renomear(Request $request, Arquivo $arquivo)
    {
        $this->authorize('update', $arquivo);

        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $antigo = $arquivo->nome;
        $arquivo->update(['nome' => $request->nome]);

        AuditHelper::log('renamed_file', 'Renomeou o arquivo "' . $antigo . '" para "' . $arquivo->nome . '"'); // ✅ Log de renomeação

        return back()->with('success', 'Nome do arquivo atualizado!');
    }

    /**
     * Remove um arquivo do sistema.
     */
    public function destroy(Arquivo $arquivo)
    {
        $this->authorize('delete', $arquivo);

        if (Storage::disk('public')->exists($arquivo->caminho)) {
            Storage::disk('public')->delete($arquivo->caminho);
        }

        $nome = $arquivo->nome;
        $paciente = $arquivo->paciente->nome ?? 'Desconhecido';

        $arquivo->delete();

        AuditHelper::log('deleted_file', 'Removeu o arquivo "' . $nome . '" do paciente ' . $paciente); // ✅ Log de exclusão

        return back()->with('success', 'Arquivo excluído com sucesso.');
    }
}
