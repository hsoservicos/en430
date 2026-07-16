<?php
/**
 * AvaliacaoTest — Testes do sistema de avaliação
 * Geração, respostas, correção, resultado, progresso
 */
class AvaliacaoTest extends PHPUnit\Framework\TestCase
{
    private PDO $db;
    private int $estudanteId;

    protected function setUp(): void
    {
        limparBanco();
        $this->db = getTestDb();
        
        // Criar estudante de teste
        $hash = hashSenha('1234');
        $this->db->prepare("INSERT INTO estudantes (nome, email, senha_hash) VALUES (?, ?, ?)")
           ->execute(['Avaliação Teste', 'avaliacao@teste.com', $hash]);
        $this->estudanteId = (int)$this->db->lastInsertId();
        
        // Iniciar sessão
        $_SESSION['estudante_id'] = $this->estudanteId;
        $_SESSION['estudante_nome'] = 'Avaliação Teste';
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    // ─── GERAÇÃO DE AVALIAÇÃO ───────────────────────────────

    public function testGerarAvaliacao(): void
    {
        $db = $this->db;
        $questoes = $db->query("SELECT id FROM questoes")->fetchAll(PDO::FETCH_COLUMN);
        $this->assertGreaterThanOrEqual(10, count($questoes), 'Deve haver questões disponíveis');
        
        $selecionadas = array_rand(array_flip($questoes), 5);
        $questoesIds = json_encode(array_values($selecionadas));
        
        $db->prepare("INSERT INTO avaliacoes (estudante_id, total_questoes, questoes_ids, status) VALUES (?, ?, ?, 'em_andamento')")
           ->execute([$this->estudanteId, 5, $questoesIds]);
        
        $avaliacaoId = $db->lastInsertId();
        
        $stmt = $db->prepare("SELECT * FROM avaliacoes WHERE id = ?");
        $stmt->execute([$avaliacaoId]);
        $avaliacao = $stmt->fetch();
        
        $this->assertNotFalse($avaliacao);
        $this->assertEquals('em_andamento', $avaliacao['status']);
        $this->assertEquals(5, $avaliacao['total_questoes']);
        $this->assertEquals($this->estudanteId, $avaliacao['estudante_id']);
    }

    public function testGerarAvaliacaoComFiltroDificuldade(): void
    {
        $db = $this->db;
        
        // Contar questões fáceis
        $countFacil = $db->query("SELECT COUNT(*) FROM questoes WHERE dificuldade = 'Fácil'")->fetchColumn();
        $this->assertGreaterThan(0, (int)$countFacil, 'Deve haver questões fáceis');
        
        // Verificar se o filtro SQL funciona
        $stmt = $db->prepare("SELECT id FROM questoes WHERE dificuldade = ?");
        $stmt->execute(['Fácil']);
        $faceis = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $this->assertCount((int)$countFacil, $faceis);
    }

    public function testGerarAvaliacaoEvitarRepeticao(): void
    {
        $db = $this->db;
        
        // Criar avaliação anterior
        $questoesAnteriores = json_encode([1, 2, 3]);
        $resultado = json_encode(['1' => ['correta' => true], '2' => ['correta' => false], '3' => ['correta' => true]]);
        $respostas = json_encode(['1' => 'A', '2' => 'B', '3' => 'C']);
        
        $db->prepare("INSERT INTO avaliacoes (estudante_id, total_questoes, questoes_ids, respostas, resultado, pontuacao, status) VALUES (?, ?, ?, ?, ?, ?, 'concluido')")
           ->execute([$this->estudanteId, 3, $questoesAnteriores, $respostas, $resultado, 2]);
        
        // Buscar questões já respondidas
        $respondidas = [];
        $stmt = $db->prepare("SELECT questoes_ids FROM avaliacoes WHERE estudante_id = ? AND status = 'concluido'");
        $stmt->execute([$this->estudanteId]);
        foreach ($stmt->fetchAll() as $av) {
            $ids = json_decode($av['questoes_ids'], true) ?? [];
            $respondidas = array_merge($respondidas, $ids);
        }
        
        $this->assertContains(1, $respondidas);
        $this->assertContains(2, $respondidas);
        $this->assertContains(3, $respondidas);
        
        // Selecionar novas questões
        $todas = $db->query("SELECT id FROM questoes")->fetchAll(PDO::FETCH_COLUMN);
        $naoRespondidas = array_diff($todas, $respondidas);
        
        $this->assertNotEmpty($naoRespondidas, 'Deve haver questões não respondidas');
        $this->assertNotContains(1, $naoRespondidas);
    }

    // ─── RESPOSTAS E CORREÇÃO ───────────────────────────────

    public function testSubmeterRespostas(): void
    {
        $db = $this->db;
        
        // Criar avaliação
        $questoesIds = json_encode([2, 4]); // questão 2 resp=B, questão 4 resp=B
        $db->prepare("INSERT INTO avaliacoes (estudante_id, total_questoes, questoes_ids, status) VALUES (?, ?, ?, 'em_andamento')")
           ->execute([$this->estudanteId, 2, $questoesIds]);
        $avaliacaoId = $db->lastInsertId();
        
        // Simular respostas
        $respostas = json_encode(['2' => 'B', '4' => 'B']);
        $questoesIds = json_decode($questoesIds, true);
        
        $pontuacao = 0;
        $resultado = [];
        
        foreach ($questoesIds as $qid) {
            $respEstudante = json_decode($respostas, true)[(string)$qid] ?? '';
            
            $stmt = $db->prepare("SELECT * FROM questoes WHERE id = ?");
            $stmt->execute([$qid]);
            $questao = $stmt->fetch();
            
            $correta = ($respEstudante === $questao['resposta']);
            if ($correta) $pontuacao++;
            
            $resultado[(string)$qid] = [
                'correta' => $correta,
                'resposta_estudante' => $respEstudante,
                'resposta_correta' => $questao['resposta'],
            ];
        }
        
        // Atualizar avaliação
        $db->prepare("UPDATE avaliacoes SET respostas = ?, resultado = ?, pontuacao = ?, status = 'concluido', data_fim = CURRENT_TIMESTAMP WHERE id = ?")
           ->execute([$respostas, json_encode($resultado), $pontuacao, $avaliacaoId]);
        
        // Verificar resultado
        $stmt = $db->prepare("SELECT * FROM avaliacoes WHERE id = ?");
        $stmt->execute([$avaliacaoId]);
        $avaliacao = $stmt->fetch();
        
        $this->assertEquals('concluido', $avaliacao['status']);
        $this->assertEquals(2, $avaliacao['pontuacao']);
        $this->assertNotNull($avaliacao['data_fim']);
    }

    public function testSubmeterRespostasParciais(): void
    {
        $db = $this->db;
        
        $questoesIds = json_encode([1, 2, 3]);
        $db->prepare("INSERT INTO avaliacoes (estudante_id, total_questoes, questoes_ids, status) VALUES (?, ?, ?, 'em_andamento')")
           ->execute([$this->estudanteId, 3, $questoesIds]);
        $avaliacaoId = $db->lastInsertId();
        
        // Uma resposta certa (q2=B), duas erradas
        $respostas = json_encode(['1' => 'A', '2' => 'B', '3' => 'D']);
        $questoesIds = json_decode($questoesIds, true);
        
        $pontuacao = 0;
        foreach ($questoesIds as $qid) {
            $resp = json_decode($respostas, true)[(string)$qid] ?? '';
            $stmt = $db->prepare("SELECT resposta FROM questoes WHERE id = ?");
            $stmt->execute([$qid]);
            $correta = $stmt->fetchColumn();
            if ($resp === $correta) $pontuacao++;
        }
        
        // q1 resp=B (errado A), q2 resp=B (certo B), q3 resp=B (errado D)
        // Esperado: 1 acerto
        $this->assertEquals(1, $pontuacao);
    }

    public function testSubmeterSemResposta(): void
    {
        $db = $this->db;
        
        $questoesIds = json_encode([1, 2]);
        $db->prepare("INSERT INTO avaliacoes (estudante_id, total_questoes, questoes_ids, status) VALUES (?, ?, ?, 'em_andamento')")
           ->execute([$this->estudanteId, 2, $questoesIds]);
        
        // Sem respostas (vazio)
        $respostas = json_encode(['1' => '', '2' => '']);
        $questoesIds = json_decode($questoesIds, true);
        
        $pontuacao = 0;
        foreach ($questoesIds as $qid) {
            $resp = json_decode($respostas, true)[(string)$qid] ?? '';
            $stmt = $db->prepare("SELECT resposta FROM questoes WHERE id = ?");
            $stmt->execute([$qid]);
            $correta = $stmt->fetchColumn();
            if ($resp === $correta) $pontuacao++;
        }
        
        $this->assertEquals(0, $pontuacao, 'Sem respostas, pontuação deve ser 0');
    }

    // ─── RESULTADO ──────────────────────────────────────────

    public function testResultadoCalculaPorcentagem(): void
    {
        $this->assertEquals(50.0, calcularPorcentagem(10, 20));
        $this->assertEquals(100.0, calcularPorcentagem(20, 20));
        $this->assertEquals(0.0, calcularPorcentagem(0, 20));
        $this->assertEquals(0.0, calcularPorcentagem(5, 0));
        $this->assertEquals(75.0, calcularPorcentagem(15, 20));
    }

    public function testBadgeNota(): void
    {
        $this->assertStringContainsString('Excelente', badgeNota(95));
        $this->assertStringContainsString('Excelente', badgeNota(100));
        $this->assertStringContainsString('Muito bom', badgeNota(75));
        $this->assertStringContainsString('Muito bom', badgeNota(70));
        $this->assertStringContainsString('Bom', badgeNota(60));
        $this->assertStringContainsString('Bom', badgeNota(50));
        $this->assertStringContainsString('estudar', badgeNota(30));
        $this->assertStringContainsString('estudar', badgeNota(0));
    }

    // ─── PROGRESSO ──────────────────────────────────────────

    public function testHistoricoAvaliacoes(): void
    {
        $db = $this->db;
        
        // Criar 3 avaliações concluídas
        for ($i = 0; $i < 3; $i++) {
            $db->prepare("INSERT INTO avaliacoes (estudante_id, total_questoes, questoes_ids, respostas, resultado, pontuacao, status) VALUES (?, ?, ?, ?, ?, ?, 'concluido')")
               ->execute([$this->estudanteId, 5, '[]', '{}', '{}', rand(1, 5)]);
        }
        
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM avaliacoes WHERE estudante_id = ? AND status = 'concluido'");
        $stmt->execute([$this->estudanteId]);
        $this->assertEquals(3, (int)$stmt->fetchColumn());
    }

    public function testEstatisticasEstudante(): void
    {
        $db = $this->db;
        
        $db->prepare("INSERT INTO avaliacoes (estudante_id, total_questoes, questoes_ids, respostas, resultado, pontuacao, status) VALUES (?, ?, ?, ?, ?, ?, 'concluido')")
           ->execute([$this->estudanteId, 10, '[]', '{}', '{}', 7]);
        
        $db->prepare("INSERT INTO avaliacoes (estudante_id, total_questoes, questoes_ids, respostas, resultado, pontuacao, status) VALUES (?, ?, ?, ?, ?, ?, 'concluido')")
           ->execute([$this->estudanteId, 10, '[]', '{}', '{}', 5]);
        
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_avaliacoes,
                COALESCE(AVG(pontuacao * 100.0 / total_questoes), 0) as media_acertos,
                COALESCE(MAX(pontuacao * 100.0 / total_questoes), 0) as melhor_nota
            FROM avaliacoes 
            WHERE estudante_id = ? AND status = 'concluido'
        ");
        $stmt->execute([$this->estudanteId]);
        $stats = $stmt->fetch();
        
        $this->assertEquals(2, (int)$stats['total_avaliacoes']);
        $this->assertEquals(60.0, (float)$stats['media_acertos']);
        $this->assertEquals(70.0, (float)$stats['melhor_nota']);
    }

    public function testViewProgresso(): void
    {
        $db = $this->db;
        
        // Inserir avaliações
        $db->prepare("INSERT INTO avaliacoes (estudante_id, total_questoes, questoes_ids, respostas, resultado, pontuacao, status) VALUES (?, ?, ?, ?, ?, ?, 'concluido')")
           ->execute([$this->estudanteId, 20, json_encode([1,2,3,4,5,6,7,8,9,10]), '{}', '{}', 15]);
        
        $stmt = $db->query("SELECT * FROM vw_progresso_estudante WHERE id = ?");
        $stmt->execute([$this->estudanteId]);
        $progresso = $stmt->fetch();
        
        $this->assertNotFalse($progresso);
        $this->assertEquals(1, (int)$progresso['total_avaliacoes']);
        $this->assertEquals(75.0, (float)$progresso['media_acertos']);
        $this->assertEquals(75.0, (float)$progresso['melhor_desempenho']);
    }

    // ─── DATA HORA ──────────────────────────────────────────

    public function testFormatacaoDatas(): void
    {
        $this->assertEquals('-', dataHoraBrasil(null));
        $this->assertEquals('-', dataBrasil(null));
        $this->assertEquals('15/07/2026 14:30', dataHoraBrasil('2026-07-15 14:30:00'));
        $this->assertEquals('01/01/2026', dataBrasil('2026-01-01'));
    }

    // ─── SESSÃO ─────────────────────────────────────────────

    public function testEstaLogado(): void
    {
        $_SESSION = ['estudante_id' => 1, 'estudante_nome' => 'Teste'];
        $this->assertTrue(estaLogado());
        
        $_SESSION = [];
        $this->assertFalse(estaLogado());
    }

    public function testGetEstudanteId(): void
    {
        $_SESSION = ['estudante_id' => 42];
        $this->assertEquals(42, getEstudanteId());
        
        $_SESSION = [];
        $this->assertEquals(0, getEstudanteId());
    }
}
