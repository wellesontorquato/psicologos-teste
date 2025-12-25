<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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

        // ✅ use config() (mais seguro em produção)
        $conn = config('database.default', 'mysql');
        $cfg  = config("database.connections.$conn");

        $host = $cfg['host'] ?? env('DB_HOST');
        $port = (string)($cfg['port'] ?? env('DB_PORT', 3306));
        $db   = $cfg['database'] ?? env('DB_DATABASE');
        $user = $cfg['username'] ?? env('DB_USERNAME');
        $pass = $cfg['password'] ?? env('DB_PASSWORD');

        // 🧠 caminho absoluto evita problema de PATH no cron
        $mysqldump = '/usr/bin/mysqldump';

        $cmd = [
            $mysqldump,
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '-h', $host,
            '-P', $port,
            '-u', $user,
            "-p{$pass}",
            $db
        ];

        $this->info("⏳ Gerando backup do banco...");
        Log::info('[BackupMysql] Iniciando dump', [
            'host' => $host,
            'port' => $port,
            'db'   => $db,
            'user' => $user,
            'dest' => $caminhoS3,
        ]);

        try {
            $process = new Process($cmd);
            $process->setTimeout(60 * 20); // 20 min
            $process->run();

            if (!$process->isSuccessful()) {
                $err = $process->getErrorOutput() ?: 'Sem stderr';
                $this->error('❌ Erro ao gerar backup: ' . $err);
                Log::error('[BackupMysql] Falha no mysqldump', ['stderr' => $err]);
                return Command::FAILURE;
            }

            $conteudoSQL = $process->getOutput();

            if (trim($conteudoSQL) === '') {
                $this->error('❌ Dump vazio (sem conteúdo).');
                Log::error('[BackupMysql] Dump vazio');
                return Command::FAILURE;
            }

            // ✅ envia ao S3 (Contabo S3)
            Storage::disk('s3')->put($caminhoS3, $conteudoSQL);
            $this->info("✅ Backup salvo em: $caminhoS3");
            Log::info('[BackupMysql] Upload concluído', ['dest' => $caminhoS3, 'bytes' => strlen($conteudoSQL)]);

            // mantém apenas 2
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
                    Log::info('[BackupMysql] Backup antigo removido', ['file' => $arquivo]);
                }
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌ Exceção no backup: ' . $e->getMessage());
            Log::error('[BackupMysql] Exceção', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}
