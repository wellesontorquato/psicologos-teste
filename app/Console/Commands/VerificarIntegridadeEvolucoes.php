<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class VerificarIntegridadeEvolucoes extends Command
{
    protected $signature = 'diagnostico:auditoria';
    protected $description = 'Diagnóstico de permissão de acesso à auditoria para usuários';

    public function handle()
    {
        $this->info("🔍 Iniciando diagnóstico de acesso à rota /auditoria...\n");

        $usuarios = User::all();
        $erros = 0;

        foreach ($usuarios as $user) {
            $this->line("👤 Verificando: {$user->name} ({$user->email})");

            $admin = $user->is_admin ? '✅ Sim' : '❌ Não';
            $this->line("   • É admin? {$admin}");

            // Tenta simular a verificação da gate
            $resultado = Gate::forUser($user)->allows('view-auditoria') ? '✅ Liberado' : '❌ Negado';

            $this->line("   • Acesso via Gate: {$resultado}");

            if (!$user->is_admin || !Gate::forUser($user)->allows('view-auditoria')) {
                $this->warn("   ⚠️ Inconsistência detectada para {$user->email}");
                $erros++;
            }

            $this->line("");
        }

        if ($erros === 0) {
            $this->info("🎉 Todos os usuários admin possuem acesso à auditoria corretamente.");
        } else {
            $this->warn("⚠️ {$erros} inconsistência(s) encontrada(s). Corrija para garantir acesso à rota /auditoria.");
        }

        return Command::SUCCESS;
    }
}
