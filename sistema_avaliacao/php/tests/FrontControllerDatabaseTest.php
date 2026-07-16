<?php
/**
 * FrontControllerDatabaseTest — Testes das rotas que acessam banco de dados
 *
 * O banco de teste (avaliacao_test_ctrl.db) é gerenciado explicitamente:
 *   1. criarBancoTeste()  — cria schema + 10 questões
 *   2. Pre-popula dados (estudante, avaliação) via PDO direto
 *   3. captureRouteOutput() — usa o DB existente e executa a rota
 *   4. Verifica output e/ou estado do DB
 *   5. limparBancoTeste() — remove o DB no tearDown
 *
 * NOTA: Não usa @runInSeparateProcess para que o xdebug conte a cobertura
 * destes testes. O session_destroy() + session_start() em captureRouteOutput()
 * garante estado de sessão limpo entre execuções.
 */

class FrontControllerDatabaseTest extends PHPUnit\Framework\TestCase
{
    private string $projectRoot;
    private string $dbFile;
    private bool $dbCreated = false;

    protected function setUp(): void
    {
        $this->projectRoot = dirname(__DIR__);
        $this->dbFile = $this->projectRoot . '/avaliacao_test_ctrl.db';
        $this->dbCreated = false;
    }

    protected function tearDown(): void
    {
        if ($this->dbCreated) {
            $this->limparBancoTeste();
        }
    }

    private function dbPath(): string
    {
        return $this->dbFile;
    }

    /**
     * Cria banco SQLite de teste com schema completo e 10 questões
     */
    private function criarBancoTeste(): PDO
    {
        @unlink($this->dbPath());
        $this->dbCreated = true;
        $pdo = new PDO("sqlite:" . $this->dbPath());
        $pdo->exec("PRAGMA foreign_keys = ON");
        $pdo->exec("
            CREATE TABLE estudantes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL, telefone TEXT, data_nascimento TEXT,
                email TEXT UNIQUE NOT NULL, senha_hash TEXT NOT NULL,
                data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE questoes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                modulo INTEGER NOT NULL CHECK(modulo BETWEEN 1 AND 10),
                dificuldade TEXT NOT NULL DEFAULT 'Médio',
                texto TEXT NOT NULL, opcao_a TEXT NOT NULL, opcao_b TEXT NOT NULL,
                opcao_c TEXT NOT NULL, opcao_d TEXT NOT NULL,
                resposta TEXT NOT NULL CHECK(resposta IN ('A','B','C','D'))
            );
            CREATE TABLE avaliacoes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                estudante_id INTEGER NOT NULL,
                data_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP, data_fim TIMESTAMP,
                total_questoes INTEGER NOT NULL, questoes_ids TEXT NOT NULL,
                respostas TEXT, resultado TEXT, pontuacao INTEGER,
                status TEXT DEFAULT 'em_andamento',
                FOREIGN KEY (estudante_id) REFERENCES estudantes(id)
            );
        ");
        $pdo->exec("INSERT INTO questoes (modulo, dificuldade, texto, opcao_a, opcao_b, opcao_c, opcao_d, resposta) VALUES
            (1,'Fácil','Teórica das Necessidades?','Florence','Wanda Horta','Jean Watson','Leininger','B'),
            (1,'Médio','SAE regulamentada pela?','240/2000','358/2009','429/2012','564/2017','B'),
            (2,'Fácil','Etapas da SAE?','3','4','5','6','C'),
            (2,'Difícil','Diagnóstico privativo do?','Médico','Enfermeiro','Técnico','Farmacêutico','B'),
            (3,'Fácil','Afebril significa?','Com febre','Sem febre','Febre alta','Febre baixa','B'),
            (3,'Médio','Cianose significa?','Palidez','Azulada','Avermelhado','Amarelado','B'),
            (4,'Fácil','Certos da medicação?','5','7','9','10','B'),
            (5,'Fácil','Absorção mais rápida?','IM','EV','SC','ID','B'),
            (1,'Difícil','Dor intensa pós-op?','Lazer','Conforto','Autoestima','Religiosidade','B'),
            (2,'Médio','NANDA-I é para?','Intervenções','Diagnósticos','Resultados','Medicamentos','B')");
        return $pdo;
    }

    private function limparBancoTeste(): void
    {
        @unlink($this->dbPath());
        $this->dbCreated = false;
    }

    /**
     * Cria um estudante de teste e retorna seus dados
     */
    private function criarEstudanteTeste(PDO $pdo): array
    {
        $hash = password_hash('1234', PASSWORD_BCRYPT, ['cost' => 12]);
        $pdo->prepare("INSERT INTO estudantes (nome, telefone, email, senha_hash) VALUES (?,?,?,?)")
            ->execute(['Maria Teste', '11999999999', 'maria@teste.com', $hash]);
        $stmt = $pdo->query("SELECT * FROM estudantes WHERE email='maria@teste.com'");
        return $stmt->fetch();
    }

    /**
     * Cria uma avaliação concluída no banco de teste
     */
    private function criarAvaliacaoTeste(PDO $pdo, int $estudanteId): int
    {
        $ids = $pdo->query("SELECT id FROM questoes LIMIT 3")->fetchAll(PDO::FETCH_COLUMN);
        $respostas = []; $resultado = [];
        foreach ($ids as $qid) {
            $respostas[(string)$qid] = 'B';
            $resultado[(string)$qid] = ['correta'=>true,'resposta_estudante'=>'B','resposta_correta'=>'B',
                'texto'=>'Teste','dificuldade'=>'Médio','modulo'=>1,
                'opcoes'=>['A'=>'A','B'=>'B','C'=>'C','D'=>'D']];
        }
        $pdo->prepare("INSERT INTO avaliacoes (estudante_id,total_questoes,questoes_ids,respostas,resultado,pontuacao,status)
            VALUES (?,?,?,?,?,?,'concluido')")
            ->execute([$estudanteId, count($ids), json_encode($ids),
                json_encode($respostas), json_encode($resultado), count($ids)]);
        return (int)$pdo->lastInsertId();
    }

    /**
     * Executa uma rota no index.php e retorna o HTML capturado.
     * O banco de teste deve ter sido criado via criarBancoTeste() antes.
     *
     * NOTA: Não usa @runInSeparateProcess. A sessão é totalmente resetada
     * com session_destroy() + session_start() para garantir isolamento.
     */
    private function captureRouteOutput(string $route, string $method = 'GET',
        array $post = [], array $session = [], array $server = []): string
    {
        if (!defined('PHPUNIT_TEST')) define('PHPUNIT_TEST', true);
        putenv('APP_DB_PATH=' . $this->dbPath());

        // Resetar sessão completamente para evitar contaminação entre testes
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        session_start();

        $_GET = ['url' => $route];
        $_POST = $post;
        $_SESSION = $session;
        $_SERVER = array_merge([
            'REQUEST_METHOD' => $method, 'SCRIPT_NAME' => '/index.php',
            'SERVER_PROTOCOL' => 'HTTP/1.1', 'HTTP_HOST' => '127.0.0.1',
            'SERVER_NAME' => '127.0.0.1', 'SERVER_PORT' => '80',
            'REMOTE_ADDR' => '127.0.0.1', 'REQUEST_URI' => '/' . $route,
            'QUERY_STRING' => '',
        ], $server);

        ob_start();
        try { include $this->projectRoot . '/index.php'; }
        finally { $output = ob_get_clean(); }
        session_write_close();
        closeDB(); // Resetar cache PDO para proximo teste
        return $output ?: '';
    }

    // ═══════════════════════════════════════════════════════
    // TESTES (sem @runInSeparateProcess para contar no coverage)
    // ═══════════════════════════════════════════════════════

    public function testCadastroComErroValidacao(): void
    {
        $this->criarBancoTeste();
        $output = $this->captureRouteOutput('cadastro', 'POST', [
            'csrf_token' => 'test_token',
            'nome' => '', 'email' => 'invalido', 'senha' => '12', 'confirmar_senha' => '34',
        ], ['csrf_token' => 'test_token']);
        $this->assertStringContainsString('obrigatório', $output);
        $this->assertStringContainsString('Email inválido', $output);
        $this->assertStringContainsString('mínimo', $output);
        $this->assertStringContainsString('não conferem', $output);
    }

    public function testCadastroComDadosValidos(): void
    {
        $this->criarBancoTeste();
        $this->captureRouteOutput('cadastro', 'POST', [
            'csrf_token' => 'test_token_123',
            'nome' => 'Novo Aluno', 'email' => 'aluno@teste.com',
            'senha' => '1234', 'confirmar_senha' => '1234',
        ], ['csrf_token' => 'test_token_123']);
        $pdo = new PDO("sqlite:" . $this->dbPath());
        $this->assertEquals(1, (int)$pdo->query("SELECT COUNT(*) FROM estudantes")->fetchColumn());
        $pdo = null;
    }

    public function testLoginSenhaErrada(): void
    {
        $pdo = $this->criarBancoTeste();
        $this->criarEstudanteTeste($pdo);
        $pdo = null;
        $output = $this->captureRouteOutput('login', 'POST', [
            'csrf_token' => 'test_token',
            'email' => 'maria@teste.com', 'senha' => 'senha_errada',
        ], ['csrf_token' => 'test_token']);
        $this->assertStringContainsString('Email ou senha incorretos', $output);
    }

    public function testPainelRequerLogin(): void
    {
        $this->criarBancoTeste();
        $output = $this->captureRouteOutput('painel');
        $this->assertStringContainsString('Faça login', $output);
    }

    public function testPainelLogadoExibeDados(): void
    {
        $pdo = $this->criarBancoTeste();
        $estudante = $this->criarEstudanteTeste($pdo);
        $this->criarAvaliacaoTeste($pdo, (int)$estudante['id']);
        $pdo = null;
        $output = $this->captureRouteOutput('painel', 'GET', [], [
            'estudante_id' => (int)$estudante['id'],
            'estudante_nome' => $estudante['nome'],
        ]);
        $this->assertStringContainsString('Maria Teste', $output);
        $this->assertStringContainsString('Avaliações Realizadas', $output);
        $this->assertStringContainsString('Histórico', $output);
    }

    public function testProgressoLogado(): void
    {
        $pdo = $this->criarBancoTeste();
        $estudante = $this->criarEstudanteTeste($pdo);
        $this->criarAvaliacaoTeste($pdo, (int)$estudante['id']);
        $pdo = null;
        $output = $this->captureRouteOutput('progresso', 'GET', [], [
            'estudante_id' => (int)$estudante['id'],
        ]);
        $this->assertStringContainsString('Meu Progresso', $output);
    }

    public function testNovaAvaliacaoLogado(): void
    {
        $pdo = $this->criarBancoTeste();
        $estudante = $this->criarEstudanteTeste($pdo);
        $pdo = null;
        $this->captureRouteOutput('nova-avaliacao', 'POST', [
            'csrf_token' => 'test_token', 'dificuldade' => 'todas',
        ], [
            'estudante_id' => (int)$estudante['id'],
            'csrf_token' => 'test_token',
        ]);
        $pdoCheck = new PDO("sqlite:" . $this->dbPath());
        $this->assertEquals(1, (int)$pdoCheck->query("SELECT COUNT(*) FROM avaliacoes")->fetchColumn());
        $pdoCheck = null;
    }

    public function testAdminLoginSenhaErrada(): void
    {
        $this->criarBancoTeste();
        $output = $this->captureRouteOutput('admin-login', 'POST', [
            'csrf_token' => 'test_token', 'senha_admin' => 'senha_errada',
        ], ['csrf_token' => 'test_token']);
        $this->assertStringContainsString('incorreta', $output);
    }

    public function testAdminLoginSenhaCorreta(): void
    {
        $this->criarBancoTeste();
        $output = $this->captureRouteOutput('admin-login', 'POST', [
            'csrf_token' => 'test_token', 'senha_admin' => ADMIN_SECRET,
        ], ['csrf_token' => 'test_token']);
        $this->assertEmpty($output, 'Admin login deve redirecionar sem renderizar view');
        $this->assertTrue(!empty($_SESSION['admin_authenticated']));
    }

    public function testAvaliacaoNaoEncontrada(): void
    {
        $pdo = $this->criarBancoTeste();
        $estudante = $this->criarEstudanteTeste($pdo);
        $pdo = null;
        $output = $this->captureRouteOutput('avaliacao/99999', 'GET', [], [
            'estudante_id' => (int)$estudante['id'],
        ]);
        $this->assertEmpty($output, 'Deve redirecionar sem renderizar view');
        $flashes = $_SESSION['_flash'] ?? [];
        $this->assertNotEmpty($flashes, 'Deve haver flash message de erro');
        $this->assertStringContainsString('não encontrada', $flashes[0]['mensagem'] ?? '');
    }
}
