const cron = require("node-cron");
const { exec } = require("child_process");
const fs = require("fs");
const path = require("path");

process.on("uncaughtException", (err) => console.error("🔥 uncaughtException:", err));
process.on("unhandledRejection", (err) => console.error("🔥 unhandledRejection:", err));

function resolveProjectDir() {
  // 1) variável (se você quiser setar)
  if (process.env.APP_DIR) return process.env.APP_DIR;

  // 2) Railway workdir (se existir)
  if (process.env.RAILWAY_WORKDIR) return process.env.RAILWAY_WORKDIR;

  // 3) padrões comuns
  const candidates = [
    process.cwd(),
    "/var/www/html",
    "/app",
  ];

  for (const dir of candidates) {
    try {
      if (dir && fs.existsSync(path.join(dir, "artisan"))) return dir;
    } catch (_) {}
  }

  // fallback
  return process.cwd();
}

const projectDir = resolveProjectDir();
const lockDir = path.join(projectDir, "storage/app/locks");
const logDir = path.join(projectDir, "storage/logs/cron");

console.log("✅ Cron boot em:", new Date().toString());
console.log("📁 projectDir:", projectDir);

function ensureDirs() {
  // Se não existir storage (deploy quebrado), não explode sem log.
  try {
    if (!fs.existsSync(lockDir)) fs.mkdirSync(lockDir, { recursive: true });
    if (!fs.existsSync(logDir)) fs.mkdirSync(logDir, { recursive: true });
  } catch (e) {
    console.error("❌ Falha criando dirs de lock/log:", e.message);
    console.error("📁 lockDir:", lockDir);
    console.error("📁 logDir :", logDir);
    // não dar throw aqui mantém o processo vivo pra você ver o erro no log
  }
}
ensureDirs();

function nowStamp() {
  const d = new Date();
  const pad = (n) => String(n).padStart(2, "0");
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}_${pad(d.getHours())}-${pad(d.getMinutes())}-${pad(d.getSeconds())}`;
}

function safeWrite(filePath, content) {
  try {
    fs.writeFileSync(filePath, content);
  } catch (e) {
    console.error(`❌ Falha ao escrever arquivo ${filePath}:`, e.message);
  }
}

function runWithLock(commandName, artisanCommand) {
  const lockFile = path.join(lockDir, `${commandName}.lock`);
  const logFile = path.join(logDir, `${commandName}-${nowStamp()}.log`);

  // Confere se artisan existe (melhor erro do que “silêncio”)
  const artisanPath = path.join(projectDir, "artisan");
  if (!fs.existsSync(artisanPath)) {
    console.error(`❌ Não achei o artisan em: ${artisanPath}`);
    safeWrite(logFile, `ERROR: artisan não encontrado em ${artisanPath}\n`);
    return;
  }

  // Lock simples com stale unlock
  try {
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

    safeWrite(lockFile, String(Date.now()));
  } catch (e) {
    console.error(`❌ Falha no lock de ${commandName}:`, e.message);
    return;
  }

  console.log(`⏰ Disparou ${commandName} em ${new Date().toString()}`);
  console.log(`🚀 Executando: ${artisanCommand}`);

  // Importante: sempre roda do diretório do projeto
  const cmd = `cd "${projectDir}" && php artisan ${artisanCommand} -v --no-interaction`;

  exec(cmd, { timeout: 1000 * 60 * 30, shell: "/bin/bash" }, (error, stdout, stderr) => {
    try {
      const content =
        `### WHEN: ${new Date().toISOString()}\n` +
        `### CMD: ${cmd}\n\n` +
        `### STDOUT\n${stdout || ""}\n\n` +
        `### STDERR\n${stderr || ""}\n\n` +
        (error ? `### ERROR\n${error.message}\n` : "");

      safeWrite(logFile, content);

      if (error) console.error(`❌ Erro ${artisanCommand}: ${error.message}`);
      if (stderr) console.error(`⚠️ Stderr ${artisanCommand}: ${stderr}`);
      console.log(`✅ Log salvo em: ${logFile}`);
    } finally {
      try {
        if (fs.existsSync(lockFile)) fs.unlinkSync(lockFile);
      } catch (_) {}
    }
  });
}

const TZ = process.env.TZ || "America/Recife";

// (Opcional) valida cron expressions no boot
function schedule(expr, name, fn) {
  if (!cron.validate(expr)) {
    console.error(`❌ Cron inválido (${name}): ${expr}`);
    return;
  }
  cron.schedule(expr, fn, { timezone: TZ });
  console.log(`✅ Agendado (${TZ}): ${name} -> ${expr}`);
}

schedule("0 */4 * * *", "lembretes-enviar", () => runWithLock("lembretes-enviar", "lembretes:enviar"));
schedule("30 7 * * *", "checar-sessoes-nao-pagas", () => runWithLock("checar-sessoes-nao-pagas", "checar:sessoes-nao-pagas"));
schedule("0 7 * * *", "checar-aniversariantes", () => runWithLock("checar-aniversariantes", "checar:aniversariantes"));
schedule("10 4 * * *", "limpar-auditoria-antiga", () => runWithLock("limpar-auditoria-antiga", "auditoria:limpar-antigos"));
schedule("0 */6 * * *", "backup-mysql-diario", () => runWithLock("backup-mysql-diario", "backup:mysql"));
schedule("5 20 * * *", "backup-summary", () => runWithLock("backup-summary", "backup:summary"));

setInterval(() => console.log("💓 cron alive", new Date().toISOString(), "TZ=", TZ), 60_000);
