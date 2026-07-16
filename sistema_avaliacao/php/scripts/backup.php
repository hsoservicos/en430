<?php
/**
 * backup.php — Script de Backup do Banco SQLite (PHP)
 * 
 * Versão portável do backup.sh: funciona em Windows e Linux.
 * Uso: php scripts/backup.php [comando]
 * 
 * Comandos:
 *   php scripts/backup.php              → Backup manual
 *   php scripts/backup.php --auto       → Backup automático (silencioso)
 *   php scripts/backup.php --list       → Listar backups
 *   php scripts/backup.php --clean      → Apagar backups antigos (>30 dias)
 *   php scripts/backup.php --cron-install → Exibir linha para crontab
 * 
 * Agendamento Linux (crontab):
 *   0 3 * * * cd /var/www/enfermagem/sistema_avaliacao/php && php scripts/backup.php --auto
 * 
 * Agendamento Windows (Agendador de Tarefas):
 *   Programas: C:\xampp\php\php.exe
 *   Argumentos: C:\xampp\htdocs\en430\sistema_avaliacao\php\scripts\backup.php --auto
 *   Iniciar em: C:\xampp\htdocs\en430\sistema_avaliacao\php
 */

// ─── CONFIGURAÇÕES ──────────────────────────────────────────
$baseDir = realpath(__DIR__ . '/..');
$dbPath = $baseDir . '/avaliacao.db';
$backupDir = $baseDir . '/backups';
$maxDays = 30; // Manter backups por 30 dias

// ─── FUNÇÕES ────────────────────────────────────────────────

/**
 * Verificar integridade do banco SQLite
 */
function verificarIntegridade(string $dbPath): bool {
    try {
        $db = new PDO("sqlite:$dbPath");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $result = $db->query('PRAGMA integrity_check')->fetchColumn();
        return $result === 'ok';
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Obter tamanho do banco em MB
 */
function tamanhoBanco(string $dbPath): float {
    if (!file_exists($dbPath)) return 0;
    return round(filesize($dbPath) / 1048576, 2);
}

/**
 * Realizar o backup via VACUUM INTO (consistente)
 */
function realizarBackup(string $dbPath, string $backupDir, bool $silencioso = false): bool {
    if (!file_exists($dbPath)) {
        if (!$silencioso) echo "❌ ERRO: Banco de dados não encontrado em $dbPath\n";
        return false;
    }

    // Verificar integridade
    if (!verificarIntegridade($dbPath)) {
        if (!$silencioso) echo "❌ ERRO: Banco de dados corrompido! Backup cancelado.\n";
        return false;
    }

    $date = date('Ymd_His');
    $file = "$backupDir/avaliacao_$date.db.gz";

    try {
        // VACUUM INTO cria uma cópia consistente
        $db = new PDO("sqlite:$dbPath");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $tmpFile = str_replace('.gz', '', $file);
        $db->exec("VACUUM INTO " . $db->quote($tmpFile));
        $db = null;

        if (!file_exists($tmpFile)) {
            if (!$silencioso) echo "❌ ERRO: Falha ao criar backup temporário.\n";
            return false;
        }

        // Comprimir com gzip
        $conteudo = file_get_contents($tmpFile);
        $comprimido = gzencode($conteudo, 9);
        file_put_contents($file, $comprimido);
        unlink($tmpFile);

        $size = round(filesize($file) / 1048576, 2);
        if (!$silencioso) echo "✅ Backup concluído: " . basename($file) . " ({$size} MB)\n";

        // Limpar backups antigos
        limparBackupsAntigos($backupDir, $silencioso);

        return true;
    } catch (Exception $e) {
        if (!$silencioso) echo "❌ ERRO: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * Limpar backups com mais de maxDays
 */
function limparBackupsAntigos(string $backupDir, bool $silencioso = false): void {
    $removidos = 0;
    $agora = time();
    $maxAge = 30 * 86400; // 30 dias em segundos

    $files = glob("$backupDir/avaliacao_*.db.gz");
    foreach ($files as $file) {
        if (($agora - filemtime($file)) > $maxAge) {
            unlink($file);
            $removidos++;
        }
    }

    if ($removidos > 0 && !$silencioso) {
        echo "🧹 Removidos $removidos backup(s) antigos.\n";
    }
}

/**
 * Listar backups existentes
 */
function listarBackups(string $backupDir): void {
    $files = glob("$backupDir/avaliacao_*.db.gz");
    
    echo "📋 Backups disponíveis:\n\n";
    
    if (empty($files)) {
        echo "   Nenhum backup encontrado.\n";
        return;
    }

    // Ordenar por data (mais recente primeiro)
    rsort($files);
    
    $totalSize = 0;
    foreach ($files as $file) {
        $basename = basename($file);
        $size = round(filesize($file) / 1048576, 2);
        $date = date('d/m/Y H:i', filemtime($file));
        $totalSize += filesize($file);
        echo "  📦 $basename ({$size} MB) — $date\n";
    }

    echo "\n   Total: " . count($files) . " backup(s) — " . round($totalSize / 1048576, 2) . " MB\n";
}

/**
 * Exibir instrução para crontab
 */
function mostrarInstrucaoCron(string $baseDir): void {
    $scriptsDir = __DIR__;
    echo "📋 Instrução para adicionar ao crontab:\n\n";
    echo "  Execute: crontab -e\n\n";
    echo "  E adicione a linha:\n\n";
    echo "  # Backup diário do banco SQLite EN430 (03:00)\n";
    echo "  0 3 * * * cd $baseDir && php $scriptsDir/backup.php --auto\n\n";
    echo "  Para verificar: crontab -l\n";
}

// ═══════════════════════════════════════════════════════════════
// EXECUÇÃO PRINCIPAL
// ═══════════════════════════════════════════════════════════════

// Criar diretório de backup se não existir
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$comando = $argv[1] ?? '';

switch ($comando) {
    case '--auto':
        realizarBackup($dbPath, $backupDir, true);
        break;

    case '--list':
        listarBackups($backupDir);
        break;

    case '--clean':
        $removidos = 0;
        $files = glob("$backupDir/avaliacao_*.db.gz");
        foreach ($files as $file) {
            unlink($file);
            $removidos++;
        }
        echo "🧹 Removidos $removidos backup(s).\n";
        break;

    case '--cron-install':
        mostrarInstrucaoCron($baseDir);
        break;

    case '--status':
        echo "📊 Status do banco de dados:\n\n";
        if (file_exists($dbPath)) {
            $size = tamanhoBanco($dbPath);
            $integro = verificarIntegridade($dbPath);
            echo "  📁 Arquivo: " . basename($dbPath) . "\n";
            echo "  💾 Tamanho: {$size} MB\n";
            echo "  ✅ Integridade: " . ($integro ? "OK" : "CORROMPIDO!") . "\n";
            
            try {
                $db = new PDO("sqlite:$dbPath");
                $questoes = $db->query("SELECT COUNT(*) FROM questoes")->fetchColumn();
                echo "  📝 Questões: " . number_format($questoes, 0, ',', '.') . "\n";
                echo "\n";
                listarBackups($backupDir);
            } catch (Exception $e) {
                echo "  ❌ Erro ao consultar banco: " . $e->getMessage() . "\n";
            }
        } else {
            echo "  ❌ Banco de dados não encontrado em $dbPath\n";
        }
        break;

    default:
        echo "🔐 Backup do Sistema EN_430\n";
        echo dirname(__DIR__) . "\n\n";
        
        $horario = date('d/m/Y H:i:s');
        echo "📅 Iniciado em: $horario\n";
        
        if (file_exists($dbPath)) {
            $size = tamanhoBanco($dbPath);
            echo "💾 Tamanho do banco: {$size} MB\n";
            $integro = verificarIntegridade($dbPath);
            echo "✅ Integridade: " . ($integro ? "OK" : "CORROMPIDO!") . "\n\n";
            realizarBackup($dbPath, $backupDir);
        } else {
            echo "❌ ERRO: Banco de dados não encontrado!\n";
            exit(1);
        }
        
        echo "\n📋 Use --list para listar backups disponíveis.\n";
        echo "📋 Use --status para verificar o estado do banco.\n";
        echo "📋 Use --cron-install para configurar backup automático.\n";
        break;
}
