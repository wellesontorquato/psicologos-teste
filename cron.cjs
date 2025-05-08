const cron = require('node-cron');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

// 🗂️ Diretório seguro dentro do Laravel para os arquivos de lock
const lockDir = '/var/www/html/storage/app/locks';

// Certifica que o diretório existe
if (!fs.existsSync(lockDir)) {
    fs.mkdirSync(lockDir, { recursive: true });
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

// 🟢 TESTE: Lembretes a cada minuto (substitui o agendamento fixo das 08:00)
cron.schedule('0 8 * * *', () => {
    runWithLock('lembretes-enviar', 'lembretes:enviar');
});

// ✅ (mantém os demais agendamentos normais)
cron.schedule('30 7 * * *', () => {
    runWithLock('checar-sessoes-nao-pagas', 'checar:sessoes-nao-pagas');
});
cron.schedule('0 7 * * *', () => {
    runWithLock('checar-aniversariantes', 'checar:aniversariantes');
});
