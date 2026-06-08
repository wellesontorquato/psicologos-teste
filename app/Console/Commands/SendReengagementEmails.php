<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ReengagementEmailNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendReengagementEmails extends Command
{
    protected $signature = 'users:send-reengagement
                            {--limit=50 : Quantidade máxima de usuários}
                            {--sleep=1 : Intervalo em segundos entre envios}
                            {--dry-run : Apenas lista os usuários, sem enviar}';

    protected $description = 'Envia e-mail de recaptação para usuários que ainda não verificaram o e-mail';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $sleep = (int) $this->option('sleep');
        $dryRun = (bool) $this->option('dry-run');

        $query = User::query()
            ->whereNull('email_verified_at')
            ->whereNotNull('email')
            ->orderBy('id');

        $total = (clone $query)->count();

        $this->info("Usuários sem e-mail verificado encontrados: {$total}");
        $this->info("Limite desta execução: {$limit}");

        if ($dryRun) {
            $this->warn('Modo DRY-RUN ativo. Nenhum e-mail será enviado.');
        }

        $sent = 0;
        $failed = 0;

        $users = $query->limit($limit)->get();

        foreach ($users as $user) {
            try {
                if ($dryRun) {
                    $this->line("[DRY-RUN] {$user->id} - {$user->email}");
                    continue;
                }

                $user->notify(new ReengagementEmailNotification());

                $sent++;

                $this->info("Enviado para: {$user->email}");

                Log::info('E-mail de recaptação enviado', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);

                if ($sleep > 0) {
                    sleep($sleep);
                }
            } catch (\Throwable $e) {
                $failed++;

                $this->error("Falha ao enviar para {$user->email}: {$e->getMessage()}");

                Log::error('Falha ao enviar e-mail de recaptação', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'erro' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info('Finalizado.');
        $this->info("Enviados: {$sent}");
        $this->info("Falhas: {$failed}");

        return self::SUCCESS;
    }
}