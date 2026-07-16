<?php
/**
 * bootstrap.php — Bootstrap para testes do Sistema de Avaliação
 * 
 * Configura o ambiente de teste com banco SQLite em arquivo temporário.
 */

// ─── Configurar ambiente de teste ───────────────────────────
putenv('APP_ENV=testing');
putenv('APP_DEBUG=1');

// ─── Carregar arquivos principais ───────────────────────────
$baseDir = dirname(__DIR__);

require_once $baseDir . '/config.php';
require_once $baseDir . '/db.php';
require_once $baseDir . '/functions.php';

// ─── Configurar banco de teste ──────────────────────────────
$testDbPath = $baseDir . '/avaliacao_test.db';

// Remove banco de teste anterior se existir
if (file_exists($testDbPath)) {
    unlink($testDbPath);
}

// Criar banco de teste com schema e dados
try {
    $pdo = new PDO("sqlite:$testDbPath", null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    $pdo->exec("PRAGMA foreign_keys = ON");
    $pdo->exec("PRAGMA journal_mode = MEMORY");
    
    // Schema
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS estudantes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            telefone TEXT,
            data_nascimento TEXT,
            email TEXT UNIQUE NOT NULL,
            senha_hash TEXT NOT NULL,
            data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS questoes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            modulo INTEGER NOT NULL CHECK(modulo BETWEEN 1 AND 10),
            dificuldade TEXT NOT NULL DEFAULT 'Médio' CHECK(dificuldade IN ('Fácil','Médio','Difícil')),
            texto TEXT NOT NULL,
            opcao_a TEXT NOT NULL,
            opcao_b TEXT NOT NULL,
            opcao_c TEXT NOT NULL,
            opcao_d TEXT NOT NULL,
            resposta TEXT NOT NULL CHECK(resposta IN ('A','B','C','D'))
        );
        CREATE TABLE IF NOT EXISTS avaliacoes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            estudante_id INTEGER NOT NULL,
            data_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_fim TIMESTAMP,
            total_questoes INTEGER NOT NULL,
            questoes_ids TEXT NOT NULL,
            respostas TEXT,
            resultado TEXT,
            pontuacao INTEGER,
            status TEXT DEFAULT 'em_andamento' CHECK(status IN ('em_andamento','concluido')),
            FOREIGN KEY (estudante_id) REFERENCES estudantes(id)
        );
        CREATE INDEX IF NOT EXISTS idx_questoes_modulo ON questoes(modulo);
        CREATE INDEX IF NOT EXISTS idx_avaliacoes_estudante ON avaliacoes(estudante_id);

        CREATE VIEW IF NOT EXISTS vw_progresso_estudante AS
        SELECT 
            e.id, e.nome, e.email,
            COUNT(a.id) AS total_avaliacoes,
            COALESCE(AVG(a.pontuacao * 100.0 / a.total_questoes), 0) AS media_acertos,
            COALESCE(MAX(a.pontuacao * 100.0 / a.total_questoes), 0) AS melhor_desempenho
        FROM estudantes e
        LEFT JOIN avaliacoes a ON a.estudante_id = e.id AND a.status = 'concluido'
        GROUP BY e.id;
    ");
    
    // Inserir 10 questões de teste
    $pdo->exec("
        INSERT INTO questoes (modulo, dificuldade, texto, opcao_a, opcao_b, opcao_c, opcao_d, resposta) VALUES
        (1, 'Fácil', 'Qual teórica desenvolveu a Teoria das Necessidades Humanas Básicas?', 'Florence Nightingale', 'Wanda de Aguiar Horta', 'Jean Watson', 'Madeleine Leininger', 'B'),
        (1, 'Médio', 'A SAE é regulamentada pela Resolução COFEN:', '240/2000', '358/2009', '429/2012', '564/2017', 'B'),
        (2, 'Fácil', 'Quantas etapas compõem a SAE?', '3', '4', '5', '6', 'C'),
        (2, 'Difícil', 'O diagnóstico de enfermagem é privativo do:', 'Médico', 'Enfermeiro', 'Técnico', 'Farmacêutico', 'B'),
        (3, 'Fácil', 'O termo afebril significa:', 'Com febre', 'Sem febre', 'Febre alta', 'Febre baixa', 'B'),
        (3, 'Médio', 'Cianose significa:', 'Palidez', 'Coloração azulada', 'Avermelhado', 'Amarelado', 'B'),
        (4, 'Fácil', 'Quantos certos na administração de medicamentos?', '5', '7', '9', '10', 'B'),
        (5, 'Fácil', 'Via com absorção mais rápida:', 'Intramuscular', 'Endovenosa', 'Subcutânea', 'Intradérmica', 'B'),
        (1, 'Difícil', 'Paciente pós-operatório com dor intensa. Qual necessidade priorizar?', 'Lazer', 'Conforto e alívio da dor', 'Autoestima', 'Religiosidade', 'B'),
        (2, 'Médio', 'NANDA-I é a taxonomia para:', 'Intervenções', 'Diagnósticos', 'Resultados', 'Medicamentos', 'B');
    ");
    
    // Armazenar conexão em cache estático da função
    // (escopo fechado para evitar vazamento de variável global)
    _setTestDb($pdo);
    
} catch (PDOException $e) {
    echo "\n❌ Erro ao criar banco de teste: " . $e->getMessage() . "\n";
    echo "   Arquivo: {$testDbPath}\n\n";
    exit(1);
}

/**
 * Armazena a conexão PDO de teste em cache estático
 */
function _setTestDb(PDO $pdo): void {
    $GLOBALS['_test_pdo'] = $pdo;
}

/**
 * Retorna a conexão PDO para o banco de teste
 */
function getTestDb(): PDO {
    if (!isset($GLOBALS['_test_pdo'])) {
        throw new RuntimeException('Banco de teste não foi inicializado. Verifique tests/bootstrap.php');
    }
    return $GLOBALS['_test_pdo'];
}

/**
 * Cria um estudante de teste e retorna seus dados
 */
function criarEstudanteTeste(): array {
    $db = getTestDb();
    $hash = hashSenha('teste123');
    $db->prepare("INSERT INTO estudantes (nome, email, senha_hash) VALUES (?, ?, ?)")
       ->execute(['Aluno Teste', 'aluno@teste.com', $hash]);
    
    $stmt = $db->query("SELECT * FROM estudantes WHERE email = 'aluno@teste.com'");
    return $stmt->fetch();
}

/**
 * Limpa todas as tabelas entre testes (mantém questões)
 */
function limparBanco(): void {
    $db = getTestDb();
    $db->exec("DELETE FROM avaliacoes");
    $db->exec("DELETE FROM estudantes");
}
