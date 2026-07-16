<?php
/**
 * migrate.php — Migração do Banco de Dados
 * 
 * Cria o schema SQLite e popula com questões de múltipla escolha
 * para os 10 módulos de Introdução à Enfermagem (EN_430).
 * 
 * Uso: php scripts/migrate.php
 */

// Guard para evitar execução quando incluído por outro script
$isMainScript = (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'migrate.php');

if (!$isMainScript) {
    return;
}

$startTime = microtime(true);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/questions_data.php';

echo "========================================\n";
echo "  🗄️  MIGRAÇÃO DO BANCO DE DADOS\n";
echo "  Sistema de Avaliação — EN_430 (PHP)\n";
echo "========================================\n\n";

// ═══════════════════════════════════════════════════════════════
// SCHEMA
// ═══════════════════════════════════════════════════════════════

echo "📦 Criando schema...\n";

$db = getDB();

$db->exec("
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

echo "   ✅ Schema criado com sucesso!\n\n";

// ═══════════════════════════════════════════════════════════════
// VERIFICAR SE JÁ EXISTEM QUESTÕES
// ═══════════════════════════════════════════════════════════════

$existentes = $db->query("SELECT COUNT(*) FROM questoes")->fetchColumn();

if ($existentes > 0) {
    echo "⚠️  Já existem {$existentes} questões no banco.\n";
    echo "   Para recriar, execute: php scripts/recriar_questoes.php\n\n";
    echo "📊 Total: {$existentes} questões\n";
    echo "⏱️  Tempo: " . round(microtime(true) - $startTime, 2) . "s\n";
    echo "✅ Migração concluída!\n";
    exit;
}

// ═══════════════════════════════════════════════════════════════
// INSERIR QUESTÕES — Agrupadas por módulo
// ═══════════════════════════════════════════════════════════════

echo "📝 Inserindo questões...\n\n";

$modulosQuestoes = [
    1 => $questoesModulo1,
    2 => $questoesModulo2,
    3 => $questoesModulo3,
    4 => $questoesModulo4,
    5 => $questoesModulo5,
    6 => $questoesModulo6,
    7 => $questoesModulo7,
    8 => $questoesModulo8,
    9 => $questoesModulo9,
    10 => $questoesModulo10,
];

$modulos = [
    1 => 'Teoria das Necessidades Básicas',
    2 => 'SAE',
    3 => 'Anotação e Terminologias',
    4 => 'Vias Enterais',
    5 => 'Vias Parenterais',
    6 => 'Assepsia, Antissepsia, Higiene',
    7 => 'Curativos, Feridas, Coberturas',
    8 => 'Oxigenoterapia e Cateterismos',
    9 => 'Exames, Coletas, Medidas',
    10 => 'Admissão, Alta, Contenção, Pós-Morte',
];

$totalInseridas = 0;
$distribuicao = ['Fácil' => 0, 'Médio' => 0, 'Difícil' => 0];

$stmt = $db->prepare("
    INSERT INTO questoes (modulo, dificuldade, texto, opcao_a, opcao_b, opcao_c, opcao_d, resposta) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$db->beginTransaction();

foreach ($modulosQuestoes as $modulo => $questoes) {
    $countModulo = 0;
    foreach ($questoes as $q) {
        $stmt->execute([$modulo, $q[0], $q[1], $q[2], $q[3], $q[4], $q[5], $q[6]]);
        $totalInseridas++;
        $distribuicao[$q[0]]++;
        $countModulo++;
    }
    echo "   Módulo {$modulo} ({$modulos[$modulo]}): {$countModulo} questões\n";
}

$db->commit();

$tempo = round(microtime(true) - $startTime, 2);

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  ✅ MIGRAÇÃO CONCLUÍDA!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "  📊 Total: {$totalInseridas} questões\n\n";
echo "  📊 Por dificuldade:\n";
foreach (['Fácil', 'Médio', 'Difícil'] as $nivel) {
    $qtd = $distribuicao[$nivel] ?? 0;
    $pct = $totalInseridas > 0 ? round($qtd * 100 / $totalInseridas, 1) : 0;
    $icon = ['Fácil' => '🟢', 'Médio' => '🟡', 'Difícil' => '🔴'][$nivel];
    echo "     {$icon} {$nivel}: {$qtd} ({$pct}%)\n";
}
echo "\n  📁 Banco: " . realpath(DB_PATH) . "\n";
echo "  ⏱️  Tempo: {$tempo}s\n\n";
echo "  🚀 php -S 0.0.0.0:5000 -t .\n\n";
