const cron = require('node-cron');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

// 🗂️ Diretório onde ficarão os lock files
const lockDir = path.join(__dirname, 'locks');

// Certifica que o diretório existe
if (!fs.existsSync(lockDir)) {
    fs.mkdirSync(lockDir);
}

// Função genérica com lock para qualquer comando
function runWithLock(commandName, artisanCommand) {
    const lockFile = path.join(lockDir, `${commandName}.lock`);

    if (fs.existsSync(lockFile)) {
        console.log(`⛔ ${commandName} já está rodando. Abortando para evitar duplicação.`);
        return;
    }

    // Cria o lock
    fs.writeFileSync(lockFile, 'locked');
    console.log(`🚀 Executando ${artisanCommand}`);

    exec(`php artisan ${artisanCommand}`, (error, stdout, stderr) => {
        if (error) {
            console.error(`❌ Erro ${artisanCommand}: ${error.message}`);
        }
        if (stderr) {
            console.error(`⚠️ Stderr ${artisanCommand}: ${stderr}`);
        }
        console.log(`✅ Resultado ${artisanCommand}: ${stdout}`);

        // Remove o lock
        fs.unlinkSync(lockFile);
    });
}

// 🚀 Sessões não pagas - EXECUTA A CADA MINUTO para teste
cron.schedule('* * * * *', () => {
    console.log('🟢 [TESTE] Disparando checar:sessoes-nao-pagas');
    runWithLock('checar-sessoes-nao-pagas', 'checar:sessoes-nao-pagas');
});

// 🚀 Lembretes - EXECUTA A CADA MINUTO para teste
cron.schedule('* * * * *', () => {
    console.log('🟢 [TESTE] Disparando lembretes:enviar');
    runWithLock('lembretes-enviar', 'lembretes:enviar');
});

// 🚀 Aniversariantes - EXECUTA A CADA MINUTO para teste
cron.schedule('* * * * *', () => {
    console.log('🟢 [TESTE] Disparando checar:aniversariantes');
    runWithLock('checar-aniversariantes', 'checar:aniversariantes');
});
