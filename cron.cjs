const cron = require('node-cron');
const { exec } = require('child_process');

// 🚀 Sessões não pagas - EXECUTA A CADA MINUTO para teste
cron.schedule('* * * * *', () => {
    console.log('🟢 [TESTE] Executando checar:sessoes-nao-pagas');
    exec('php artisan checar:sessoes-nao-pagas', (error, stdout, stderr) => {
        if (error) {
            console.error(`❌ Erro checar:sessoes-nao-pagas: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`⚠️ Stderr checar:sessoes-nao-pagas: ${stderr}`);
            return;
        }
        console.log(`✅ Resultado checar:sessoes-nao-pagas: ${stdout}`);
    });
});

// 🚀 Lembretes - EXECUTA A CADA MINUTO para teste
cron.schedule('* * * * *', () => {
    console.log('🟢 [TESTE] Executando lembretes:enviar');
    exec('php artisan lembretes:enviar', (error, stdout, stderr) => {
        if (error) {
            console.error(`❌ Erro lembretes:enviar: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`⚠️ Stderr lembretes:enviar: ${stderr}`);
            return;
        }
        console.log(`✅ Resultado lembretes:enviar: ${stdout}`);
    });
});

// 🚀 Aniversariantes - EXECUTA A CADA MINUTO para teste
cron.schedule('* * * * *', () => {
    console.log('🟢 [TESTE] Executando checar:aniversariantes');
    exec('php artisan checar:aniversariantes', (error, stdout, stderr) => {
        if (error) {
            console.error(`❌ Erro checar:aniversariantes: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`⚠️ Stderr checar:aniversariantes: ${stderr}`);
            return;
        }
        console.log(`✅ Resultado checar:aniversariantes: ${stdout}`);
    });
});
