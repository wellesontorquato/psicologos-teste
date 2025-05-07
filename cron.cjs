const cron = require('node-cron');
const { exec } = require('child_process');

// ✅ Laravel Schedule: roda a cada minuto para checar os comandos agendados
cron.schedule('* * * * *', () => {
    console.log('🚀 Executando php artisan schedule:run');
    exec('php artisan schedule:run >> /dev/null 2>&1', (error, stdout, stderr) => {
        if (error) {
            console.error(`❌ Erro: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`⚠️ Stderr: ${stderr}`);
            return;
        }
        console.log(`✅ Resultado: ${stdout}`);
    });
});
