import cron from 'node-cron';
import { exec } from 'child_process';

console.log('⏰ cron.js está rodando...');

cron.schedule('* * * * *', () => {
    console.log('🚀 Executando lembretes:enviar...');
    exec('php artisan lembretes:enviar', (error, stdout, stderr) => {
        if (error) {
            console.error(`❌ Erro em lembretes:enviar: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`⚠️ STDERR lembretes:enviar: ${stderr}`);
            return;
        }
        console.log(`✅ lembretes:enviar output:\n${stdout}`);
    });

    console.log('🚀 Executando checar:sessoes-nao-pagas...');
    exec('php artisan checar:sessoes-nao-pagas', (error, stdout, stderr) => {
        if (error) {
            console.error(`❌ Erro em checar:sessoes-nao-pagas: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`⚠️ STDERR checar:sessoes-nao-pagas: ${stderr}`);
            return;
        }
        console.log(`✅ checar:sessoes-nao-pagas output:\n${stdout}`);
    });

    console.log('🚀 Executando checar:aniversariantes...');
    exec('php artisan checar:aniversariantes', (error, stdout, stderr) => {
        if (error) {
            console.error(`❌ Erro em checar:aniversariantes: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`⚠️ STDERR checar:aniversariantes: ${stderr}`);
            return;
        }
        console.log(`✅ checar:aniversariantes output:\n${stdout}`);
    });
});
