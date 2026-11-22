<?php
/**
 * InatividadeTest — Rotina de contas inativas (LGPD, Política 8.2)
 *
 * Cobre: seleção por inatividade, aviso por e-mail, carência, exclusão,
 * reativação por novo acesso, dry-run e o transporte de e-mail em modo log.
 */

class InatividadeTest extends PHPUnit\Framework\TestCase
{
    private string $dbFile;
    private array $avisosEnviados = [];

    protected function setUp(): void
    {
        date_default_timezone_set('UTC');
        $this->dbFile = sys_get_temp_dir() . '/en430_inatividade_' . uniqid() . '.db';
        $this->avisosEnviados = [];

        $pdo = new PDO("sqlite:" . $this->dbFile, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec("
            CREATE TABLE estudantes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                telefone TEXT,
                data_nascimento TEXT,
                email TEXT UNIQUE NOT NULL,
                senha_hash TEXT NOT NULL,
                data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE avaliacoes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                estudante_id INTEGER NOT NULL,
                data_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_fim TIMESTAMP,
                total_questoes INTEGER NOT NULL,
                questoes_ids TEXT NOT NULL,
                respostas TEXT,
                resultado TEXT,
                pontuacao INTEGER,
                status TEXT DEFAULT 'em_andamento'
            );
            CREATE TABLE admin_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                acao TEXT NOT NULL,
                detalhes TEXT,
                data TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE notificacoes_email (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                destinatario TEXT NOT NULL,
                assunto TEXT NOT NULL,
                corpo TEXT,
                transporte TEXT NOT NULL,
                status TEXT NOT NULL,
                erro TEXT,
                criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");
        $pdo = null;

        putenv('APP_DB_PATH=' . $this->dbFile);
        closeDB();
        getDB(); // aplica ensureSchemaUpgrades (colunas de consentimento e inatividade)
    }

    protected function tearDown(): void
    {
        closeDB();
        putenv('APP_DB_PATH');
        @unlink($this->dbFile);
        $_SESSION = [];
    }

    /** Data UTC deslocada em meses/dias a partir de agora (valores negativos = passado) */
    private function dataDeslocada(int $meses = 0, int $dias = 0): string
    {
        $data = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        return $data->modify(sprintf('%+d months %+d days', $meses, $dias))->format('Y-m-d H:i:s');
    }

    private function criarConta(
        string $email,
        string $cadastro,
        ?string $ultimoAcesso = null,
        ?string $aviso = null
    ): int {
        $stmt = getDB()->prepare("INSERT INTO estudantes
            (nome, email, senha_hash, data_cadastro, ultimo_acesso_em, inatividade_aviso_em)
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Estudante Teste', $email, hashSenha('1234'), $cadastro, $ultimoAcesso, $aviso]);
        return (int)getDB()->lastInsertId();
    }

    /** Enviador falso: registra a chamada e confirma a entrega */
    private function enviadorComSucesso(): callable
    {
        return function (string $para, string $assunto, string $corpo): array {
            $this->avisosEnviados[] = ['para' => $para, 'assunto' => $assunto, 'corpo' => $corpo];
            return ['ok' => true, 'status' => 'enviado', 'erro' => null, 'transporte' => 'teste'];
        };
    }

    private function contaExiste(int $id): bool
    {
        $stmt = getDB()->prepare("SELECT COUNT(*) FROM estudantes WHERE id = ?");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    // ─── SELEÇÃO ────────────────────────────────────────────

    public function testContaAtivaNaoEntraNaRotina(): void
    {
        $id = $this->criarConta('ativa@teste.com', $this->dataDeslocada(-25), $this->dataDeslocada(0, -1));

        $resumo = processarRotinaInatividade(false, $this->enviadorComSucesso());

        $this->assertSame(0, $resumo['avisos']['candidatos']);
        $this->assertSame(0, $resumo['exclusoes']['candidatos']);
        $this->assertCount(0, $this->avisosEnviados);
        $stmt = getDB()->prepare("SELECT inatividade_aviso_em FROM estudantes WHERE id = ?");
        $stmt->execute([$id]);
        $this->assertNull($stmt->fetchColumn());
    }

    public function testContaSemAcessoHa25MesesRecebeAviso(): void
    {
        $id = $this->criarConta('inativa@teste.com', $this->dataDeslocada(-26), $this->dataDeslocada(-25));

        $resumo = processarRotinaInatividade(false, $this->enviadorComSucesso());

        $this->assertSame(1, $resumo['avisos']['candidatos']);
        $this->assertSame(1, $resumo['avisos']['enviados']);
        $this->assertCount(1, $this->avisosEnviados);
        $this->assertSame('inativa@teste.com', $this->avisosEnviados[0]['para']);
        $this->assertStringContainsString('excluída por inatividade', $this->avisosEnviados[0]['assunto']);
        $this->assertStringContainsString('login', $this->avisosEnviados[0]['corpo']);

        $stmt = getDB()->prepare("SELECT inatividade_aviso_em FROM estudantes WHERE id = ?");
        $stmt->execute([$id]);
        $this->assertNotEmpty($stmt->fetchColumn(), 'Aviso entregue deve ser registrado na conta');

        // Ainda dentro da carência: não exclui
        $this->assertTrue($this->contaExiste($id));
    }

    // ─── EXCLUSÃO ───────────────────────────────────────────

    public function testExclusaoAposCarenciaComAvisoEntregue(): void
    {
        $id = $this->criarConta('excluir@teste.com', $this->dataDeslocada(-40), $this->dataDeslocada(-35));
        getDB()->prepare("INSERT INTO avaliacoes (estudante_id, total_questoes, questoes_ids) VALUES (?, 20, '[]')")
            ->execute([$id]);

        // 1ª execução: envia o aviso
        processarRotinaInatividade(false, $this->enviadorComSucesso());

        // Carência de 30 dias já transcorrida
        getDB()->prepare("UPDATE estudantes SET inatividade_aviso_em = ? WHERE id = ?")
            ->execute([$this->dataDeslocada(0, -31), $id]);

        $resumo = processarRotinaInatividade(false, $this->enviadorComSucesso());

        $this->assertSame(1, $resumo['exclusoes']['excluidos']);
        $this->assertFalse($this->contaExiste($id));

        $avaliacoes = (int)getDB()->query("SELECT COUNT(*) FROM avaliacoes")->fetchColumn();
        $this->assertSame(0, $avaliacoes, 'Avaliações da conta excluída também saem');

        $acao = getDB()->query("SELECT acao FROM admin_log WHERE acao = 'exclusao_conta_inativa' ORDER BY id DESC LIMIT 1")->fetchColumn();
        $this->assertSame('exclusao_conta_inativa', $acao);
    }

    public function testContaQueVoltouAcessarSaiDaRotinaEAvisoECancelado(): void
    {
        $id = $this->criarConta(
            'voltou@teste.com',
            $this->dataDeslocada(-30),
            $this->dataDeslocada(0, -2),
            $this->dataDeslocada(0, -40)
        );

        $resumo = processarRotinaInatividade(false, $this->enviadorComSucesso());

        $this->assertSame(1, $resumo['reativadas']);
        $this->assertSame(0, $resumo['exclusoes']['candidatos']);
        $this->assertTrue($this->contaExiste($id));

        $stmt = getDB()->prepare("SELECT inatividade_aviso_em FROM estudantes WHERE id = ?");
        $stmt->execute([$id]);
        $this->assertNull($stmt->fetchColumn());
    }

    // ─── SEGURANÇA E SIMULAÇÃO ──────────────────────────────

    public function testTransporteLogNaoMarcaAvisoNemExclui(): void
    {
        $id = $this->criarConta('log@teste.com', $this->dataDeslocada(-30), $this->dataDeslocada(-25));

        // Sem enviador injetado: usa enviarEmail com MAIL_TRANSPORT=log
        $resumo = processarRotinaInatividade(false);

        $this->assertSame(1, $resumo['avisos']['candidatos']);
        $this->assertSame(0, $resumo['avisos']['enviados']);
        $this->assertSame(1, $resumo['avisos']['falhas']);

        $stmt = getDB()->prepare("SELECT inatividade_aviso_em FROM estudantes WHERE id = ?");
        $stmt->execute([$id]);
        $this->assertNull($stmt->fetchColumn(), 'Aviso simulado não conta como entregue');
        $this->assertTrue($this->contaExiste($id));

        $linha = getDB()->query("SELECT status FROM notificacoes_email ORDER BY id DESC LIMIT 1")->fetchColumn();
        $this->assertSame('simulado', $linha);
    }

    public function testDryRunNaoAlteraNada(): void
    {
        $id = $this->criarConta('dry@teste.com', $this->dataDeslocada(-30), $this->dataDeslocada(-25));

        $resumo = processarRotinaInatividade(true, $this->enviadorComSucesso());

        $this->assertTrue($resumo['dry_run']);
        $this->assertSame(1, $resumo['avisos']['candidatos']);
        $this->assertCount(0, $this->avisosEnviados);
        $this->assertTrue($this->contaExiste($id));

        $notificacoes = (int)getDB()->query("SELECT COUNT(*) FROM notificacoes_email")->fetchColumn();
        $this->assertSame(0, $notificacoes);
    }

    // ─── E-MAIL ─────────────────────────────────────────────

    public function testEnviarEmailEmModoLogRegistraHistorico(): void
    {
        $resultado = enviarEmail('teste@exemplo.com', 'Assunto de teste', 'Corpo do e-mail');

        $this->assertFalse($resultado['ok']);
        $this->assertSame('simulado', $resultado['status']);

        $linha = getDB()->query("SELECT destinatario, status FROM notificacoes_email ORDER BY id DESC LIMIT 1")->fetch();
        $this->assertSame('teste@exemplo.com', $linha['destinatario']);
        $this->assertSame('simulado', $linha['status']);
    }

    public function testEnviarEmailRecusaDestinatarioInvalido(): void
    {
        $resultado = enviarEmail('invalido', 'Assunto', 'Corpo');

        $this->assertFalse($resultado['ok']);
        $this->assertSame('erro', $resultado['status']);
    }

    // ─── RESUMO DO PAINEL ───────────────────────────────────

    public function testResumoInatividadeContaEtapas(): void
    {
        $this->criarConta('a_avisar@teste.com', $this->dataDeslocada(-30), $this->dataDeslocada(-25));
        $this->criarConta('em_carencia@teste.com', $this->dataDeslocada(-30), $this->dataDeslocada(-25), $this->dataDeslocada(0, -2));
        $this->criarConta('elegivel@teste.com', $this->dataDeslocada(-40), $this->dataDeslocada(-35), $this->dataDeslocada(0, -31));

        $resumo = resumoInatividade();

        $this->assertSame(1, $resumo['a_avisar']);
        $this->assertSame(1, $resumo['em_carencia']);
        $this->assertSame(1, $resumo['elegiveis']);
    }
}
