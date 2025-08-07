<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LimparAuditoriaAntiga extends Command
{
    protected $signature = 'auditoria:limpar-antigos';
    protected $description = 'Gera PDF dos registros antigos e limpa auditorias com mais de 30 dias';

    public function handle()
    {
        $limite = now()->subDays(30);

        $registros = DB::table('audits')
            ->where('created_at', '<', $limite)
            ->get();

        if ($registros->isEmpty()) {
            $this->info('Nenhum registro antigo de auditoria encontrado.');
            return;
        }

        // Usa a view já existente
        $pdf = Pdf::loadView('auditoria.exportar_pdf', ['logs' => $registros]);
        $agora = now();
        $nomeArquivo = 'auditoria-' . $agora->format('Y-m-d_His') . '.pdf';
        $caminhoLocal = storage_path("app/reports/{$nomeArquivo}");
        $pdf->save($caminhoLocal);

        // Caminho estruturado no bucket S3: relatorios/auditorias/ANO/MES/nome.pdf
        $caminhoBucket = "relatorios/auditorias/{$agora->format('Y')}/{$agora->format('m')}/{$nomeArquivo}";

        Storage::disk('s3')->put($caminhoBucket, file_get_contents($caminhoLocal));
        
        // Remove os registros antigos
        DB::table('audits')->where('created_at', '<', $limite)->delete();

        $this->info("✅ PDF salvo, enviado para o bucket e registros antigos foram excluídos.");
    }
}
