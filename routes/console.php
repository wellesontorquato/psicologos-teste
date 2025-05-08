<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// ⚡ Comando personalizado de inspiração (padrão Laravel)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Exibe uma citação inspiradora');

// ✅ Agendamento funcional direto via routes/console.php

// ✅ ESTE AGENDAMENTO FOI TRANSFERIDO PARA cron.cjs
// Schedule::command('lembretes:enviar')
//     ->dailyAt('08:00')
//     ->runInBackground()
//     ->onSuccess(fn () => logger('✅ lembretes:enviar executado com sucesso'))
//     ->onFailure(fn () => logger('❌ lembretes:enviar falhou'));
