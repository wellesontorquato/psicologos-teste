<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class BackupBancoMysql extends Command
{
    protected $signature = 'backup:mysql';
    protected $description = 'Gera backup do banco MySQL, envia ao S3 e mantém apenas os 2 últimos';

    public function handle()
    {
        $data = now();
        $nomeArquivo = 'backup-' . $data->format('Y-m-d_His') . '.sql';
        $caminhoS3 = "backups/mysql/{$data->format('Y')}/{$data->format('m')}/{$nomeArquivo}";
        $prefixoPasta = "backups/mysql/{$data->format('Y')}/{$data->format('m')}/";

        // Configura o comando mysqldump
        $host = env('DB_HOST');
        $port = env('DB_PORT', 3306);
        $db   = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');

        $cmd = [
            'mysqldump',
            '-h', $host,
            '-P', $port,
            '-u', $user,
            "-p{$pass}",
            $db
        ];

        $this->info("⏳ Gerando backup do banco...");

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('❌ Erro ao gerar backup: ' . $process->getErrorOutput());
            return;
        }

        $conteudoSQL = $process->getOutput();

        // Envia para o bucket
        Storage::disk('s3')->put($caminhoS3, $conteudoSQL);
        $this->info("✅ Backup salvo em: $caminhoS3");

        // Limita para manter apenas os 2 mais recentes
        $arquivos = Storage::disk('s3')->files($prefixoPasta);
        $arquivosSql = collect($arquivos)
            ->filter(fn($f) => Str::endsWith($f, '.sql'))
            ->sort()
            ->values();

        if ($arquivosSql->count() > 2) {
            $aRemover = $arquivosSql->slice(0, $arquivosSql->count() - 2);
            foreach ($aRemover as $arquivo) {
                Storage::disk('s3')->delete($arquivo);
                $this->info("🗑️ Backup antigo removido: $arquivo");
            }
        }
    }
}
