<?php
/**
 * AuthTest — Testes de autenticação
 * Cadastro, login, CSRF, hash de senha, recuperação de acesso
 */
class AuthTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        limparBanco();
        
        // Iniciar sessão para testes
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    // ─── HASH DE SENHA ──────────────────────────────────────

    public function testHashSenhaGeraHashValido(): void
    {
        $hash = hashSenha('minha_senha');
        $this->assertNotEmpty($hash, 'Hash não deve ser vazio');
        $this->assertStringStartsWith('$2y$', $hash, 'Hash deve começar com $2y$ (bcrypt)');
    }

    public function testVerificarSenhaCorreta(): void
    {
        $hash = hashSenha('senha123');
        $this->assertTrue(verificarSenha('senha123', $hash), 'Senha correta deve verificar');
    }

    public function testVerificarSenhaIncorreta(): void
    {
        $hash = hashSenha('senha123');
        $this->assertFalse(verificarSenha('senha_errada', $hash), 'Senha incorreta não deve verificar');
    }

    public function testHashCost12(): void
    {
        $start = microtime(true);
        hashSenha('teste');
        $elapsed = microtime(true) - $start;
        $this->assertGreaterThan(0.05, $elapsed, 'Hash com cost 12 deve levar >50ms');
    }

    // ─── CSRF ───────────────────────────────────────────────

    public function testGerarCsrfGeraToken(): void
    {
        $_SESSION = [];
        $token1 = generateCsrfToken();
        $this->assertNotEmpty($token1, 'Token CSRF não deve ser vazio');
        $this->assertEquals(64, strlen($token1), 'Token CSRF deve ter 64 caracteres');
    }

    public function testGerarCsrfMantemMesmoToken(): void
    {
        $_SESSION = [];
        $token1 = generateCsrfToken();
        $token2 = generateCsrfToken();
        $this->assertEquals($token1, $token2, 'Token deve ser o mesmo na mesma sessão');
    }

    public function testValidarCsrfToken(): void
    {
        $_SESSION = [];
        $token = generateCsrfToken();
        $this->assertTrue(validateCsrfToken($token), 'Token válido deve passar');
        $this->assertFalse(validateCsrfToken('token_invalido'), 'Token inválido deve falhar');
        $this->assertFalse(validateCsrfToken(''), 'Token vazio deve falhar');
    }

    public function testGetCsrfField(): void
    {
        $_SESSION = [];
        $field = getCsrfField();
        $this->assertStringContainsString('csrf_token', $field, 'Campo deve conter csrf_token');
        $this->assertStringContainsString('hidden', $field, 'Campo deve ser hidden');
        $this->assertStringContainsString('value="', $field, 'Campo deve conter value');
    }

    // ─── CADASTRO DE ESTUDANTE ──────────────────────────────

    public function testCadastroEstudante(): void
    {
        $db = getTestDb();
        $hash = hashSenha('1234');
        
        $db->prepare("INSERT INTO estudantes (nome, data_nascimento, telefone, email, senha_hash) VALUES (?, ?, ?, ?, ?)")
           ->execute(['Maria Silva', '15/04/1990', '11987654321', 'maria@teste.com', $hash]);
        
        $stmt = $db->query("SELECT * FROM estudantes WHERE email = 'maria@teste.com'");
        $estudante = $stmt->fetch();
        
        $this->assertNotFalse($estudante, 'Estudante deve ser encontrado');
        $this->assertEquals('Maria Silva', $estudante['nome']);
        $this->assertEquals('maria@teste.com', $estudante['email']);
        $this->assertEquals('15/04/1990', $estudante['data_nascimento']);
        $this->assertEquals('11987654321', $estudante['telefone']);
        $this->assertTrue(verificarSenha('1234', $estudante['senha_hash']));
    }

    public function testCadastroEmailDuplicado(): void
    {
        $db = getTestDb();
        $hash = hashSenha('1234');
        
        $db->prepare("INSERT INTO estudantes (nome, email, senha_hash) VALUES (?, ?, ?)")
           ->execute(['Primeiro', 'dup@teste.com', $hash]);
        
        $this->expectException(PDOException::class);
        $db->prepare("INSERT INTO estudantes (nome, email, senha_hash) VALUES (?, ?, ?)")
           ->execute(['Segundo', 'dup@teste.com', $hash]);
    }

    public function testCadastroDataNascimentoOpcional(): void
    {
        $db = getTestDb();
        
        // Cadastro sem data_nascimento
        $db->prepare("INSERT INTO estudantes (nome, email, senha_hash) VALUES (?, ?, ?)")
           ->execute(['Sem Data', 'semdata@teste.com', 'hash']);
        
        $stmt = $db->query("SELECT * FROM estudantes WHERE email = 'semdata@teste.com'");
        $estudante = $stmt->fetch();
        
        $this->assertNotNull($estudante);
        $this->assertNull($estudante['data_nascimento']);
    }

    public function testCadastroDataCadastroAutomatica(): void
    {
        $db = getTestDb();
        $db->prepare("INSERT INTO estudantes (nome, email, senha_hash) VALUES (?, ?, ?)")
           ->execute(['Teste Data', 'data@teste.com', 'hash']);
        
        $stmt = $db->query("SELECT data_cadastro FROM estudantes WHERE email = 'data@teste.com'");
        $data = $stmt->fetchColumn();
        
        $this->assertNotNull($data, 'Data de cadastro deve ser preenchida automaticamente');
    }

    // ─── LOGIN ──────────────────────────────────────────────

    public function testLoginSucesso(): void
    {
        $db = getTestDb();
        $hash = hashSenha('senha123');
        $db->prepare("INSERT INTO estudantes (nome, email, senha_hash) VALUES (?, ?, ?)")
           ->execute(['Login Teste', 'login@teste.com', $hash]);
        
        $stmt = $db->prepare("SELECT * FROM estudantes WHERE email = ?");
        $stmt->execute(['login@teste.com']);
        $estudante = $stmt->fetch();
        
        $this->assertNotFalse($estudante);
        $this->assertTrue(verificarSenha('senha123', $estudante['senha_hash']));
    }

    public function testLoginFalha(): void
    {
        $db = getTestDb();
        $hash = hashSenha('senha123');
        $db->prepare("INSERT INTO estudantes (nome, email, senha_hash) VALUES (?, ?, ?)")
           ->execute(['Login Teste', 'login@teste.com', $hash]);
        
        $stmt = $db->prepare("SELECT * FROM estudantes WHERE email = ?");
        $stmt->execute(['login@teste.com']);
        $estudante = $stmt->fetch();
        
        $this->assertFalse(verificarSenha('senha_errada', $estudante['senha_hash']));
    }

    public function testLoginEmailInexistente(): void
    {
        $db = getTestDb();
        $stmt = $db->prepare("SELECT * FROM estudantes WHERE email = ?");
        $stmt->execute(['nao@existe.com']);
        
        $this->assertFalse($stmt->fetch(), 'Email inexistente não deve retornar dados');
    }

    // ─── RECUPERAÇÃO DE ACESSO ──────────────────────────────

    public function testRecuperarAcessoPorNomeTelefone(): void
    {
        $db = getTestDb();
        $hash = hashSenha('1234');
        $db->prepare("INSERT INTO estudantes (nome, telefone, email, senha_hash) VALUES (?, ?, ?, ?)")
           ->execute(['João Recuperar', '11999999999', 'joao@recuperar.com', $hash]);
        
        $stmt = $db->prepare("SELECT * FROM estudantes WHERE nome = ? AND telefone = ?");
        $stmt->execute(['João Recuperar', '11999999999']);
        $estudante = $stmt->fetch();
        
        $this->assertNotFalse($estudante);
        $this->assertEquals('joao@recuperar.com', $estudante['email']);
    }

    public function testRecuperarAcessoDadosInvalidos(): void
    {
        $db = getTestDb();
        $stmt = $db->prepare("SELECT * FROM estudantes WHERE nome = ? AND telefone = ?");
        $stmt->execute(['Nome Inexistente', '11900000000']);
        
        $this->assertFalse($stmt->fetch(), 'Dados inválidos não devem encontrar conta');
    }

    public function testRedefinirSenha(): void
    {
        $db = getTestDb();
        $hash = hashSenha('senha_antiga');
        $db->prepare("INSERT INTO estudantes (nome, telefone, email, senha_hash) VALUES (?, ?, ?, ?)")
           ->execute(['Reset Teste', '11988888888', 'reset@teste.com', $hash]);
        
        // Redefinir senha
        $novaHash = hashSenha('senha_nova');
        $db->prepare("UPDATE estudantes SET senha_hash = ? WHERE nome = ? AND telefone = ?")
           ->execute([$novaHash, 'Reset Teste', '11988888888']);
        
        // Verificar nova senha
        $stmt = $db->prepare("SELECT * FROM estudantes WHERE email = ?");
        $stmt->execute(['reset@teste.com']);
        $estudante = $stmt->fetch();
        
        $this->assertTrue(verificarSenha('senha_nova', $estudante['senha_hash']));
        $this->assertFalse(verificarSenha('senha_antiga', $estudante['senha_hash']));
    }

    // ─── VALIDAÇÕES ─────────────────────────────────────────

    public function testValidarEmail(): void
    {
        $this->assertTrue(validarEmail('teste@email.com'));
        $this->assertTrue(validarEmail('aluno@enfermagem.edu.br'));
        $this->assertFalse(validarEmail(''));
        $this->assertFalse(validarEmail('invalido'));
        $this->assertFalse(validarEmail('@semnome.com'));
    }

    public function testValidarSenha(): void
    {
        $this->assertTrue(validarSenha('1234'));
        $this->assertTrue(validarSenha('senha_segura_2026'));
        $this->assertFalse(validarSenha(''));
        $this->assertFalse(validarSenha('123'));
        $this->assertFalse(validarSenha('12'));
    }
}
