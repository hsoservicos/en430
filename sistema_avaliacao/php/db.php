<?php
/**
 * db.php — Conexão com banco de dados SQLite via PDO
 */

require_once __DIR__ . '/config.php';

/**
 * Retorna referência ao PDO estático (para closeDB resetar o cache)
 */
function &getDBRef(): ?PDO {
    static $pdo = null;
    return $pdo;
}

function getDB(): PDO {
    $pdo = &getDBRef();
    
    if ($pdo === null) {
        // Permitir sobrescrita via env var para testes
        $dbPath = getenv('APP_DB_PATH') ?: DB_PATH;
        
        try {
            $pdo = new PDO("sqlite:$dbPath", null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            
            // Ativar foreign keys
            $pdo->exec("PRAGMA foreign_keys = ON");
            $pdo->exec("PRAGMA journal_mode = WAL");

            // Bancos criados antes do consentimento (LGPD) não têm estas colunas
            ensureSchemaUpgrades($pdo);
            
        } catch (PDOException $e) {
            if (defined('DEBUG') && DEBUG) {
                die("Erro de conexão: " . $e->getMessage());
            }
            die("Erro interno do servidor.");
        }
    }
    
    return $pdo;
}

/**
 * Aplica ajustes de schema em bancos já existentes.
 *
 * O entrypoint só executa a migração quando o arquivo do banco não existe;
 * por isso as colunas novas precisam ser adicionadas em tempo de conexão.
 * A operação é idempotente e barata (PRAGMA local).
 */
function ensureSchemaUpgrades(PDO $pdo): void {
    try {
        $colunas = $pdo->query("PRAGMA table_info(estudantes)")->fetchAll(PDO::FETCH_ASSOC);
        if (!$colunas) {
            return; // tabela ainda não criada
        }
        $nomes = array_column($colunas, 'name');

        if (!in_array('consentimento_em', $nomes, true)) {
            $pdo->exec("ALTER TABLE estudantes ADD COLUMN consentimento_em TIMESTAMP");
        }
        if (!in_array('consentimento_versao', $nomes, true)) {
            $pdo->exec("ALTER TABLE estudantes ADD COLUMN consentimento_versao TEXT");
        }
    } catch (PDOException $e) {
        if (defined('DEBUG') && DEBUG) {
            error_log('Falha ao aplicar ajustes de schema: ' . $e->getMessage());
        }
    }
}

/**
 * Fecha a conexão e reseta o cache estático (útil para testes)
 */
function closeDB(): void {
    $ref = &getDBRef();
    $ref = null;
}
