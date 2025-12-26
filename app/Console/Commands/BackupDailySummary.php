<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\WhatsAppNotifier;

class BackupDailySummary extends Command
{
    protected $signature = 'backup:summary';
    protected $description = 'Resumo diário dos backups MySQL';

    public function handle(): int
    {
        $disk = Storage::disk('s3');
        $now = now();

        $prefix = "backups/mysql/{$now->format('Y')}/{$now->format('m')}/";
        $today = $now->format('Y-m-d');

        $files = collect($disk->files($prefix))
            ->filter(fn ($f) =>
                str_contains($f, "backup-{$today}") &&
                (str_ends_with($f, '.sql') || str_ends_with($f, '.sql.gz'))
            )
            ->sort()
            ->values();

        $count = $files->count();

        $totalBytes = $files->sum(function ($f) use ($disk) {
            try { return $disk->size($f); } catch (\Throwable) { return 0; }
        });

        $last = $files->last();

        $msg =
            "📊 *Resumo diário de backups*\n".
            "Data: {$now->format('d/m/Y')}\n".
            "Quantidade: {$count}\n".
            "Último: ".($last ? basename($last) : '—')."\n".
            "Total: ".$this->humanBytes($totalBytes);

        app(WhatsAppNotifier::class)->send($msg);

        $this->info('Resumo enviado.');
        return Command::SUCCESS;
    }

    private function humanBytes(int $bytes): string
    {
        $units = ['B','KB','MB','GB','TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units)-1) {
            $bytes /= 1024;
            $i++;
        }
        return number_format($bytes, 2, ',', '.') . ' ' . $units[$i];
    }
}
