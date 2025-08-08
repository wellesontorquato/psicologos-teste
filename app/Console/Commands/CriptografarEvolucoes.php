<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class CriptografarEvolucoes extends Command
{
    protected $signature = 'evolucoes:criptografar
        {--file= : Caminho do arquivo de entrada (texto puro ou criptografado)}
        {--text= : Texto passado inline (usado se --file não for fornecido)}
        {--out=  : Caminho do arquivo de saída (opcional)}
        {--decrypt : Descriptografar ao invés de criptografar}
        {--trim : Aplicar trim() ao conteúdo antes de processar}';

    protected $description = 'Criptografa ou descriptografa textos sem tocar no banco. Lê de arquivo ou argumento e imprime/salva o resultado.';

    public function handle()
    {
        $file = $this->option('file');
        $text = $this->option('text');
        $out  = $this->option('out');
        $doDecrypt = (bool) $this->option('decrypt');
        $doTrim = (bool) $this->option('trim');

        // 1) Obter conteúdo de entrada
        if ($file) {
            if (!is_readable($file)) {
                $this->error("Arquivo não encontrado ou sem permissão de leitura: {$file}");
                return 1;
            }
            $content = file_get_contents($file);
            if ($content === false) {
                $this->error("Falha ao ler o arquivo: {$file}");
                return 1;
            }
        } elseif ($text !== null) {
            $content = $text;
        } else {
            $this->warn('Nenhuma entrada fornecida (--file ou --text). Lendo STDIN... (Ctrl+D para finalizar)');
            $content = stream_get_contents(STDIN);
        }

        if ($doTrim) {
            $content = trim($content);
        }

        if ($content === '' || $content === null) {
            $this->error('Conteúdo de entrada vazio.');
            return 1;
        }

        // 2) Processar (cripto/decripto)
        try {
            if ($doDecrypt) {
                $result = Crypt::decryptString($content);
            } else {
                $result = Crypt::encryptString($content);
            }
        } catch (\Throwable $e) {
            $this->error(($doDecrypt ? 'Falha ao descriptografar' : 'Falha ao criptografar') . ': ' . $e->getMessage());
            return 1;
        }

        // 3) Saída (terminal e/ou arquivo)
        if ($out) {
            $dir = dirname($out);
            if (!is_dir($dir)) {
                // tenta criar diretório se não existir
                if (!@mkdir($dir, 0775, true) && !is_dir($dir)) {
                    $this->error("Não foi possível criar o diretório de saída: {$dir}");
                    return 1;
                }
            }
            $ok = file_put_contents($out, $result);
            if ($ok === false) {
                $this->error("Falha ao escrever arquivo de saída: {$out}");
                return 1;
            }
            $this->info('✅ Operação concluída.');
            $this->line(($doDecrypt ? 'Descriptografado' : 'Criptografado') . " → {$out}");
        } else {
            // imprime no terminal apenas o resultado
            $this->line($result);
        }

        return 0;
    }
}
