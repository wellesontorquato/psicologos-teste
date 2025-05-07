const cron = require('node-cron');
const shell = require('shelljs');

// Executa o schedule:run a cada minuto
cron.schedule('* * * * *', function () {
    console.log('⏰ Executando tarefas agendadas via cron.js');
    shell.exec('php artisan schedule:run >> /dev/null 2>&1');
});
