<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;
use App\Services\WhatsAppNotifier;

class BackupBancoMysql extends Command
{
    protected $signature = 'backup:mysql';
    protected $description = 'Gera backup do banco MySQL, envia ao S3 e mantém os últimos 30';

    public function handle(): int
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

        // 🧠 evita problema de PATH no cron (Railway/Windows/etc)
        $mysqldump = env('MYSQLDUMP_PATH', 'mysqldump');

        $cmd = [
            $mysqldump,
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '-h', $host,
            '-P', $port,
            '-u', $user,
            "-p{$pass}",
            $db,
        ];

        $context = [
            'app'  => config('app.name'),
            'env'  => config('app.env'),
            'host' => $host,
            'port' => $port,
            'db'   => $db,
            'user' => $user,
            'dest' => $caminhoS3,
        ];

        $this->info("⏳ Gerando backup do banco...");
        Log::info('[BackupMysql] Iniciando dump', $context);

        try {
            $process = new Process($cmd);
            $process->setTimeout(60 * 20); // 20 min
            $process->run();

            if (!$process->isSuccessful()) {
                $err = trim($process->getErrorOutput() ?: 'Sem stderr');

                $this->error('❌ Erro ao gerar backup: ' . $err);
                Log::error('[BackupMysql] Falha no mysqldump', $context + ['stderr' => $err]);

                $this->notifyWhatsApp(
                    "Backup MySQL FALHOU (mysqldump)\n".
                    "Data: {$data->format('d/m/Y H:i:s')}\n".
                    "Destino: {$caminhoS3}\n".
                    "Erro: {$err}"
                );

                return Command::FAILURE;
            }

            $conteudoSQL = $process->getOutput();

            if (trim($conteudoSQL) === '') {
                $this->error('❌ Dump vazio (sem conteúdo).');
                Log::error('[BackupMysql] Dump vazio', $context);

                $this->notifyWhatsApp(
                    "Backup MySQL FALHOU (dump vazio)\n".
                    "Data: {$data->format('d/m/Y H:i:s')}\n".
                    "Destino: {$caminhoS3}"
                );

                return Command::FAILURE;
            }

            // 🔐 Hash do dump (integridade)
            $hash = hash('sha256', $conteudoSQL);

            // ✅ envia ao S3
            Storage::disk('s3')->put($caminhoS3, $conteudoSQL);
            Storage::disk('s3')->put($caminhoS3 . '.sha256', $hash);

            $bytes = strlen($conteudoSQL);
            $this->info("✅ Backup salvo em: $caminhoS3");
            $this->info("🔐 SHA256: $hash");

            Log::info('[BackupMysql] Upload concluído', $context + [
                'bytes' => $bytes,
                'sha256' => $hash,
            ]);

            // 🔁 Mantém apenas os 30 backups mais recentes (neste mês)
            $limite = 30;

            $arquivos = Storage::disk('s3')->files($prefixoPasta);

            $arquivosSql = collect($arquivos)
                ->filter(fn ($f) => Str::endsWith($f, '.sql'))
                ->sort() // ordena pelo nome (timestamp no nome)
                ->values();

            if ($arquivosSql->count() > $limite) {
                $aRemover = $arquivosSql->slice(0, $arquivosSql->count() - $limite);

                foreach ($aRemover as $arquivo) {
                    Storage::disk('s3')->delete($arquivo);
                    Storage::disk('s3')->delete($arquivo . '.sha256'); // se existir
                    $this->info("🗑️ Backup antigo removido: $arquivo");
                }

                Log::info('[BackupMysql] Retenção aplicada', $context + [
                    'limit' => $limite,
                    'removed' => $aRemover->count(),
                ]);
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $msg = $e->getMessage();

            $this->error('❌ Exceção no backup: ' . $msg);
            Log::error('[BackupMysql] Exceção', $context + ['error' => $msg]);

            $this->notifyWhatsApp(
                "Backup MySQL FALHOU (exceção)\n".
                "Data: {$data->format('d/m/Y H:i:s')}\n".
                "Destino: {$caminhoS3}\n".
                "Erro: {$msg}"
            );

            return Command::FAILURE;
        }
    }

    private function notifyWhatsApp(string $message): void
    {
        // Para evitar quebrar o backup caso o WhatsApp falhe,
        // a notificação nunca deve lançar exceção.
        try {
            app(WhatsAppNotifier::class)->send($message);
        } catch (\Throwable $e) {
            Log::warning('[BackupMysql] Falha ao notificar WhatsApp', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
