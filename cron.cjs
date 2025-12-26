const cron = require('node-cron');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

const projectDir = '/var/www/html';
const lockDir = path.join(projectDir, 'storage/app/locks');
const logDir  = path.join(projectDir, 'storage/logs/cron');

if (!fs.existsSync(lockDir)) fs.mkdirSync(lockDir, { recursive: true });
if (!fs.existsSync(logDir)) fs.mkdirSync(logDir, { recursive: true });

function nowStamp() {
  const d = new Date();
  const pad = (n) => String(n).padStart(2, '0');
  return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}_${pad(d.getHours())}-${pad(d.getMinutes())}-${pad(d.getSeconds())}`;
}

function runWithLock(commandName, artisanCommand) {
  const lockFile = path.join(lockDir, `${commandName}.lock`);
  const logFile = path.join(logDir, `${commandName}-${nowStamp()}.log`);

  // Se lock existe e é "velho", remove (evita travar pra sempre)
  if (fs.existsSync(lockFile)) {
    const stat = fs.statSync(lockFile);
    const ageMin = (Date.now() - stat.mtimeMs) / 60000;
    if (ageMin > 120) {
      fs.unlinkSync(lockFile);
    } else {
      console.log(`⛔ ${commandName} já está rodando. Abortando.`);
      return;
    }
  }

  fs.writeFileSync(lockFile, String(Date.now()));
  console.log(`🚀 Executando: ${artisanCommand}`);

  const cmd = `cd ${projectDir} && /usr/bin/php artisan ${artisanCommand} -v`;

  exec(cmd, { timeout: 1000 * 60 * 30 }, (error, stdout, stderr) => {
    try {
      const content =
        `### CMD: ${cmd}\n\n` +
        `### STDOUT\n${stdout || ''}\n\n` +
        `### STDERR\n${stderr || ''}\n\n` +
        (error ? `### ERROR\n${error.message}\n` : '');

      fs.writeFileSync(logFile, content);

      if (error) console.error(`❌ Erro ${artisanCommand}: ${error.message}`);
      if (stderr) console.error(`⚠️ Stderr ${artisanCommand}: ${stderr}`);
      console.log(`✅ Log salvo em: ${logFile}`);
    } finally {
      if (fs.existsSync(lockFile)) fs.unlinkSync(lockFile);
    }
  });
}

cron.schedule('0 */4 * * *', () => {runWithLock('lembretes-enviar', 'lembretes:enviar');});
cron.schedule('30 7 * * *', () => runWithLock('checar-sessoes-nao-pagas', 'checar:sessoes-nao-pagas'));
cron.schedule('0 7 * * *', () => runWithLock('checar-aniversariantes', 'checar:aniversariantes'));
cron.schedule('10 4 * * *', () => runWithLock('limpar-auditoria-antiga', 'auditoria:limpar-antigos'));
cron.schedule('0 */6 * * *', () => {runWithLock('backup-mysql-diario', 'backup:mysql');});
cron.schedule('5 20 * * *', () =>runWithLock('backup-summary', 'backup:summary'));
