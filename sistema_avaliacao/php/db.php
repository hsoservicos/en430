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
 * Fecha a conexão e reseta o cache estático (útil para testes)
 */
function closeDB(): void {
    $ref = &getDBRef();
    $ref = null;
}
