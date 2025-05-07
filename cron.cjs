const cron = require('node-cron');
const { exec } = require('child_process');

// 🕑 Sessões não pagas - Roda todo dia às 07:30
cron.schedule('30 7 * * *', () => {
    console.log('🚀 Executando checar:sessoes-nao-pagas');
    exec('php artisan checar:sessoes-nao-pagas', (error, stdout, stderr) => {
        if (error) {
            console.error(`❌ Erro: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`⚠️ Stderr: ${stderr}`);
            return;
        }
        console.log(`✅ Resultado sessoes-nao-pagas: ${stdout}`);
    });
});

// 🕑 Lembretes - Roda todo dia às 08:00
cron.schedule('0 8 * * *', () => {
    console.log('🚀 Executando lembretes:enviar');
    exec('php artisan lembretes:enviar', (error, stdout, stderr) => {
        if (error) {
            console.error(`❌ Erro: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`⚠️ Stderr: ${stderr}`);
            return;
        }
        console.log(`✅ Resultado lembretes: ${stdout}`);
    });
});

// 🕑 Aniversariantes - Roda todo dia às 07:00
cron.schedule('0 7 * * *', () => {
    console.log('🚀 Executando checar:aniversariantes');
    exec('php artisan checar:aniversariantes', (error, stdout, stderr) => {
        if (error) {
            console.error(`❌ Erro: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`⚠️ Stderr: ${stderr}`);
            return;
        }
        console.log(`✅ Resultado aniversariantes: ${stdout}`);
    });
});
