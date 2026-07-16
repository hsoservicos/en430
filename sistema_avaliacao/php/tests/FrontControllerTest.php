<?php
/**
 * FrontControllerTest — Testes unitários do front controller (index.php)
 *
 * Cada teste executa em processo separado (@runInSeparateProcess)
 * para evitar conflito de redefinição de funções.
 *
 * Cobre as rotas GET que renderizam views sem depender de banco de dados:
 *   /                 → view('index')
 *   /cadastro         → view('cadastro')
 *   /login            → view('login')
 *   /recuperar-acesso → view('recuperar_acesso')
 *   /admin-login      → view('admin_login')
 *   /admin (GET)      → view('admin_login') quando não autenticado
 *   /rota-invalida    → 404 + view('index')
 *
 * Rotas que acessam banco de dados (painel, progresso, etc.)
 * são cobertas pelo IntegrationTest via servidor HTTP real.
 */

class FrontControllerTest extends PHPUnit\Framework\TestCase
{
    private string $projectRoot;

    protected function setUp(): void
    {
        $this->projectRoot = dirname(__DIR__);
    }

    /**
     * Configura o ambiente para simular uma requisição HTTP GET
     */
    private function setUpEnvironment(string $route, array $session = []): void
    {
        $_GET = ['url' => $route];
        $_POST = [];
        $_SESSION = $session;
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'SCRIPT_NAME' => '/index.php',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_HOST' => '127.0.0.1',
            'SERVER_NAME' => '127.0.0.1',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/' . $route,
            'QUERY_STRING' => '',
        ];
    }

    /**
     * Configura ambiente para POST, iniciando sessão manualmente
     * para que o CSRF token sobreviva ao initSession() interno.
     */
    private function setUpPostEnvironment(string $route, array $post, string $csrfToken = 'test_csrf_token'): void
    {
        $_GET = ['url' => $route];
        $_POST = array_merge(['csrf_token' => $csrfToken], $post);
        $_SERVER = [
            'REQUEST_METHOD' => 'POST',
            'SCRIPT_NAME' => '/index.php',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_HOST' => '127.0.0.1',
            'SERVER_NAME' => '127.0.0.1',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/' . $route,
            'QUERY_STRING' => '',
        ];

        // Iniciar sessão manualmente para preservar $_SESSION
        // antes do include, evitando que initSession() a resete
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['csrf_token'] = $csrfToken;
    }

    /**
     * Inclui o index.php com output buffering e retorna o HTML capturado
     */
    private function captureIndexOutput(): string
    {
        ob_start();
        try {
            include $this->projectRoot . '/index.php';
        } finally {
            $output = ob_get_clean();
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        return $output ?: '';
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRotaIndex(): void
    {
        $this->setUpEnvironment('');
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('Sistema de Avaliação', $output);
        $this->assertStringContainsString('Introdução à Enfermagem', $output);
        $this->assertStringContainsString('Fazer Login', $output);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRotaCadastro(): void
    {
        $this->setUpEnvironment('cadastro');
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('Cadastro', $output);
        $this->assertStringContainsString('csrf_token', $output);
        $this->assertStringContainsString('Criar Conta', $output);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRotaLogin(): void
    {
        $this->setUpEnvironment('login');
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('Login', $output);
        $this->assertStringContainsString('csrf_token', $output);
        $this->assertStringContainsString('Entrar', $output);
        $this->assertStringContainsString('Esqueci minha senha', $output);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRotaRecuperarAcesso(): void
    {
        $this->setUpEnvironment('recuperar-acesso');
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('Recuperar', $output);
        $this->assertStringContainsString('csrf_token', $output);
        $this->assertStringContainsString('Buscar', $output);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRotaAdminLogin(): void
    {
        $this->setUpEnvironment('admin-login');
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('Acesso Administrativo', $output);
        $this->assertStringContainsString('csrf_token', $output);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRotaAdminGetSemAuth(): void
    {
        // /admin via GET sem autenticação deve mostrar tela de login admin
        $this->setUpEnvironment('admin');
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('Administrativo', $output);
        $this->assertStringContainsString('csrf_token', $output);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRota404(): void
    {
        $this->setUpEnvironment('rota-inexistente');
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('Sistema de Avaliação', $output);
        $this->assertEquals(404, http_response_code());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRotaIndexLogada(): void
    {
        // Quando logado, a página inicial ainda deve renderizar
        $this->setUpEnvironment('', [
            'estudante_id' => 1,
            'estudante_nome' => 'Maria',
        ]);
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('Sistema de Avaliação', $output);
    }

    // ═══════════════════════════════════════════════════════
    // ROTAS POST (sem acesso a BD)
    // ═══════════════════════════════════════════════════════

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testPostAdminLoginSenhaErrada(): void
    {
        $this->setUpPostEnvironment('admin-login', [
            'senha_admin' => 'senha_errada',
        ]);
        $output = $this->captureIndexOutput();

        // Deve renderizar a view de login admin com mensagem de erro
        $this->assertStringContainsString('Acesso Administrativo', $output);
        $this->assertStringContainsString('incorreta', $output);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testPostCadastroNomeVazio(): void
    {
        $this->setUpPostEnvironment('cadastro', [
            'nome' => '',
            'email' => 'teste@teste.com',
            'senha' => '1234',
            'confirmar_senha' => '1234',
        ]);
        $output = $this->captureIndexOutput();

        // Deve renderizar o cadastro com mensagem de erro
        $this->assertStringContainsString('Cadastro', $output);
        $this->assertStringContainsString('obrigatório', $output);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testPostCadastroEmailInvalido(): void
    {
        $this->setUpPostEnvironment('cadastro', [
            'nome' => 'Teste',
            'email' => 'email-invalido',
            'senha' => '1234',
            'confirmar_senha' => '1234',
        ]);
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('Cadastro', $output);
        $this->assertStringContainsString('Email inválido', $output);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testPostCadastroSenhaCurta(): void
    {
        $this->setUpPostEnvironment('cadastro', [
            'nome' => 'Teste',
            'email' => 'teste@teste.com',
            'senha' => '12',
            'confirmar_senha' => '12',
        ]);
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('Senha deve ter', $output);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testPostCadastroSenhasDiferentes(): void
    {
        $this->setUpPostEnvironment('cadastro', [
            'nome' => 'Teste',
            'email' => 'teste@teste.com',
            'senha' => '1234',
            'confirmar_senha' => '4321',
        ]);
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('não conferem', $output);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testPostCadastroMultiplosErros(): void
    {
        // Nome vazio + email inválido = múltiplos erros
        $this->setUpPostEnvironment('cadastro', [
            'nome' => '',
            'email' => 'invalido',
            'senha' => '12',
            'confirmar_senha' => '34',
        ]);
        $output = $this->captureIndexOutput();

        $this->assertStringContainsString('obrigatório', $output);
        $this->assertStringContainsString('Email inválido', $output);
        $this->assertStringContainsString('mínimo', $output);
        $this->assertStringContainsString('não conferem', $output);
    }
}
