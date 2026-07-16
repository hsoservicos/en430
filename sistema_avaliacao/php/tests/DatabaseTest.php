<?php
/**
 * DatabaseTest — Testes de banco de dados e schema
 */
class DatabaseTest extends PHPUnit\Framework\TestCase
{
    private PDO $db;

    protected function setUp(): void
    {
        $this->db = getTestDb();
    }

    public function testConexao(): void
    {
        $this->assertNotNull($this->db, 'Conexão PDO deve ser estabelecida');
        $this->assertInstanceOf(PDO::class, $this->db);
    }

    public function testTabelaEstudantesExiste(): void
    {
        $stmt = $this->db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='estudantes'");
        $this->assertNotFalse($stmt->fetch(), 'Tabela estudantes deve existir');
    }

    public function testTabelaQuestoesExiste(): void
    {
        $stmt = $this->db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='questoes'");
        $this->assertNotFalse($stmt->fetch(), 'Tabela questoes deve existir');
    }

    public function testTabelaAvaliacoesExiste(): void
    {
        $stmt = $this->db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='avaliacoes'");
        $this->assertNotFalse($stmt->fetch(), 'Tabela avaliacoes deve existir');
    }

    public function testSchemaViewExiste(): void
    {
        $stmt = $this->db->query("SELECT name FROM sqlite_master WHERE type='view' AND name='vw_progresso_estudante'");
        $this->assertNotFalse($stmt->fetch(), 'View vw_progresso_estudante deve existir');
    }

    public function testQuestoesPopuladas(): void
    {
        $count = $this->db->query("SELECT COUNT(*) FROM questoes")->fetchColumn();
        $this->assertGreaterThanOrEqual(10, (int)$count, 'Deve haver pelo menos 10 questões de teste');
    }

    public function testQuestoesTemDificuldade(): void
    {
        $dificuldades = $this->db->query("SELECT DISTINCT dificuldade FROM questoes")->fetchAll(PDO::FETCH_COLUMN);
        foreach (['Fácil', 'Médio', 'Difícil'] as $d) {
            $this->assertContains($d, $dificuldades, "Dificuldade '$d' deve estar presente");
        }
    }

    public function testQuestoesTemModulos(): void
    {
        $modulos = $this->db->query("SELECT DISTINCT modulo FROM questoes ORDER BY modulo")->fetchAll(PDO::FETCH_COLUMN);
        $this->assertNotEmpty($modulos, 'Deve haver pelo menos 1 módulo');
        foreach ($modulos as $m) {
            $this->assertGreaterThanOrEqual(1, (int)$m, 'Módulo deve ser >= 1');
            $this->assertLessThanOrEqual(10, (int)$m, 'Módulo deve ser <= 10');
        }
    }

    public function testRespostasValidas(): void
    {
        $stmt = $this->db->query("SELECT DISTINCT resposta FROM questoes");
        $respostas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($respostas as $r) {
            $this->assertContains($r, ['A', 'B', 'C', 'D'], "Resposta '$r' deve ser A, B, C ou D");
        }
    }

    public function testForeignKeyAtivada(): void
    {
        $stmt = $this->db->query("PRAGMA foreign_keys");
        $this->assertEquals(1, (int)$stmt->fetchColumn(), 'Foreign keys devem estar ativadas');
    }

    public function testConstraintEmailUnico(): void
    {
        $db = $this->db;
        $db->prepare("INSERT INTO estudantes (nome, email, senha_hash) VALUES (?, ?, ?)")
           ->execute(['Teste 1', 'dup@teste.com', 'hash']);
        
        $this->expectException(PDOException::class);
        $db->prepare("INSERT INTO estudantes (nome, email, senha_hash) VALUES (?, ?, ?)")
           ->execute(['Teste 2', 'dup@teste.com', 'hash']);
    }

    public function testIndicesExistem(): void
    {
        $indices = [];
        $stmt = $this->db->query("SELECT name FROM sqlite_master WHERE type='index' AND name LIKE 'idx_%'");
        while ($row = $stmt->fetch()) {
            $indices[] = $row['name'];
        }
        
        $this->assertContains('idx_questoes_modulo', $indices, 'Índice idx_questoes_modulo deve existir');
        $this->assertContains('idx_avaliacoes_estudante', $indices, 'Índice idx_avaliacoes_estudante deve existir');
    }
}
