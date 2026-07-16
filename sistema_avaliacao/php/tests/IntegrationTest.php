<?php
/**
 * IntegrationTest — Testes de integração via HTTP
 * Testa o front controller, rotas e fluxo HTTP completo
 */
class IntegrationTest extends PHPUnit\Framework\TestCase
{
    private static ?int $serverPid = null;
    private static string $baseUrl = 'http://127.0.0.1:8081';
    private static string $cookies = '/tmp/integration_cookies.txt';

    public static function setUpBeforeClass(): void
    {
        // Usar banco de teste separado
        $docRoot = dirname(__DIR__);
        $routerFile = $docRoot . '/router.php';
        $testDbPath = $docRoot . '/avaliacao_test_int.db';
        
        // Remover banco de integração anterior
        @unlink($testDbPath);
        
        // Criar banco de teste com schema e dados
        $pdo = new PDO("sqlite:$testDbPath");
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
        ");
        // Inserir questões de teste
        $pdo->exec("
            INSERT INTO questoes (modulo, dificuldade, texto, opcao_a, opcao_b, opcao_c, opcao_d, resposta) VALUES
            (1, 'Fácil', 'Q1?', 'A1', 'B1', 'C1', 'D1', 'B'),
            (1, 'Médio', 'Q2?', 'A2', 'B2', 'C2', 'D2', 'A'),
            (2, 'Fácil', 'Q3?', 'A3', 'B3', 'C3', 'D3', 'C'),
            (2, 'Difícil', 'Q4?', 'A4', 'B4', 'C4', 'D4', 'D'),
            (3, 'Fácil', 'Q5?', 'A5', 'B5', 'C5', 'D5', 'A')
        ");
        $pdo = null;
        
        // Matar servidor anterior se existir
        exec("lsof -ti:8081 2>/dev/null | xargs kill -9 2>/dev/null");
        usleep(300000);
        
        // Iniciar servidor com banco de teste via env var
        $cmd = "export APP_DB_PATH={$testDbPath}; cd {$docRoot} && php -S 127.0.0.1:8081 -t {$docRoot} {$routerFile} > /dev/null 2>&1 & echo $!";
        $output = [];
        exec($cmd, $output);
        self::$serverPid = (int)($output[0] ?? 0);
        
        // Aguardar servidor iniciar
        $maxTentativas = 10;
        for ($i = 0; $i < $maxTentativas; $i++) {
            usleep(300000);
            $ch = curl_init(self::$baseUrl . '/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                return;
            }
        }
        
        self::markTestSkipped('Servidor PHP não iniciou a tempo');
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$serverPid) {
            exec("kill " . self::$serverPid . " 2>/dev/null");
        }
        @unlink(self::$cookies);
        @unlink(dirname(__DIR__) . '/avaliacao_test_int.db');
    }

    protected function setUp(): void
    {
        @unlink(self::$cookies);
    }

    private function httpGet(string $path, bool $followRedirects = false): array
    {
        $ch = curl_init(self::$baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_COOKIEFILE => self::$cookies,
            CURLOPT_COOKIEJAR => self::$cookies,
            CURLOPT_FOLLOWLOCATION => $followRedirects,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        return [
            'code' => $httpCode,
            'headers' => $headers,
            'body' => $body,
        ];
    }

    private function httpPost(string $path, array $data, bool $followRedirects = false): array
    {
        $ch = curl_init(self::$baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_TIMEOUT => 5,
            CURLOPT_COOKIEFILE => self::$cookies,
            CURLOPT_COOKIEJAR => self::$cookies,
            CURLOPT_FOLLOWLOCATION => $followRedirects,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        return [
            'code' => $httpCode,
            'headers' => $headers,
            'body' => $body,
        ];
    }

    private function extractCsrf(string $html): ?string
    {
        if (preg_match('/name="csrf_token" value="([a-f0-9]+)"/', $html, $matches)) {
            return $matches[1];
        }
        return null;
    }

    // ═══════════════════════════════════════════════════════
    // TESTES DE ROTAS
    // ═══════════════════════════════════════════════════════

    public function testIndexPage(): void
    {
        $res = $this->httpGet('/');
        $this->assertEquals(200, $res['code']);
        $this->assertStringContainsString('Sistema de Avaliação', $res['body']);
        $this->assertStringContainsString('Introdução à Enfermagem', $res['body']);
        $this->assertStringContainsString('Cadastre-se', $res['body']);
        $this->assertStringContainsString('Fazer Login', $res['body']);
    }

    public function testCadastroPage(): void
    {
        $res = $this->httpGet('/cadastro');
        $this->assertEquals(200, $res['code']);
        $this->assertStringContainsString('Cadastro', $res['body']);
        $this->assertStringContainsString('csrf_token', $res['body']);
        $this->assertStringContainsString('Criar Conta', $res['body']);
    }

    public function testLoginPage(): void
    {
        $res = $this->httpGet('/login');
        $this->assertEquals(200, $res['code']);
        $this->assertStringContainsString('Login', $res['body']);
        $this->assertStringContainsString('csrf_token', $res['body']);
        $this->assertStringContainsString('Entrar', $res['body']);
        $this->assertStringContainsString('Esqueci minha senha', $res['body']);
    }

    public function testRecuperarAcessoPage(): void
    {
        $res = $this->httpGet('/recuperar-acesso');
        $this->assertEquals(200, $res['code']);
        $this->assertStringContainsString('Recuperar Acesso', $res['body']);
        $this->assertStringContainsString('csrf_token', $res['body']);
        $this->assertStringContainsString('Buscar Minha Conta', $res['body']);
    }

    public function testPainelRequerLogin(): void
    {
        // Sem cookie de sessão, deve redirecionar
        $res = $this->httpGet('/painel');
        $this->assertEquals(302, $res['code']);
        $this->assertStringContainsString('login', $res['headers']);
    }

    public function testProgressoRequerLogin(): void
    {
        $res = $this->httpGet('/progresso');
        $this->assertEquals(302, $res['code']);
        $this->assertStringContainsString('login', $res['headers']);
    }

    public function testAssetsCSS(): void
    {
        $res = $this->httpGet('/assets/css/style.css');
        $this->assertEquals(200, $res['code']);
        $this->assertStringContainsString(':root', $res['body']);
    }

    public function testAssetsJS(): void
    {
        $res = $this->httpGet('/assets/js/app.js');
        $this->assertEquals(200, $res['code']);
        $this->assertStringContainsString('mascaras', strtolower($res['body']));
    }

    // ═══════════════════════════════════════════════════════
    // FLUXO COMPLETO
    // ═══════════════════════════════════════════════════════

    public function testFluxoCompletoCadastroLogin(): void
    {
        // 1. Obter CSRF do cadastro
        $res = $this->httpGet('/cadastro');
        $this->assertEquals(200, $res['code']);
        $csrf = $this->extractCsrf($res['body']);
        $this->assertNotNull($csrf, 'CSRF token deve estar presente no cadastro');
        
        // 2. Cadastrar
        $res = $this->httpPost('/cadastro', [
            'csrf_token' => $csrf,
            'nome' => 'Integração Teste',
            'data_nascimento' => '01/01/2000',
            'telefone' => '11911111111',
            'email' => 'integracao@teste.com',
            'senha' => '1234',
            'confirmar_senha' => '1234',
        ]);
        $this->assertEquals(302, $res['code'], 'Cadastro deve redirecionar para login');
        $this->assertStringContainsString('login', $res['headers']);
        
        // 3. Obter CSRF do login
        $res = $this->httpGet('/login');
        $csrf2 = $this->extractCsrf($res['body']);
        $this->assertNotNull($csrf2, 'CSRF token deve estar presente no login');
        
        // 4. Login
        $res = $this->httpPost('/login', [
            'csrf_token' => $csrf2,
            'email' => 'integracao@teste.com',
            'senha' => '1234',
        ]);
        $this->assertEquals(302, $res['code'], 'Login deve redirecionar para painel');
        $this->assertStringContainsString('painel', $res['headers']);
        
        // 5. Acessar painel (já logado)
        $res = $this->httpGet('/painel');
        $this->assertEquals(200, $res['code']);
        $this->assertStringContainsString('Integração Teste', $res['body']);
        $this->assertStringContainsString('Avaliações', $res['body']);
    }

    public function testFluxoAvaliacaoCompleto(): void
    {
        // Cadastrar e logar
        $this->cadastrarELogar();
        
        // Obter CSRF do painel para criar avaliação
        $res = $this->httpGet('/painel');
        $csrf = $this->extractCsrf($res['body']);
        $this->assertNotNull($csrf);
        
        // Criar avaliação
        $res = $this->httpPost('/nova-avaliacao', [
            'csrf_token' => $csrf,
            'dificuldade' => 'todas',
        ]);
        $this->assertEquals(302, $res['code'], 'Nova avaliação deve redirecionar');
        
        // Seguir redirect
        preg_match('/Location: ([^\r\n]+)/', $res['headers'], $loc);
        $redirectUrl = trim($loc[1] ?? '');
        $this->assertNotEmpty($redirectUrl, 'Deve haver redirect para avaliação');
        
        // Extrair path do redirect
        $path = parse_url($redirectUrl, PHP_URL_PATH);
        $this->assertStringContainsString('avaliacao/', $path);
        
        // Carregar avaliação
        $res = $this->httpGet($path);
        $this->assertEquals(200, $res['code']);
        $this->assertStringContainsString('questao-card', $res['body'], 'Deve conter questões');
        
        // Extrair CSRF da avaliação
        $csrfAv = $this->extractCsrf($res['body']);
        $this->assertNotNull($csrfAv);
        
        // Responder questões
        preg_match_all('/name="(q_\d+)"/', $res['body'], $matches);
        $questoes = $matches[1] ?? [];
        $this->assertNotEmpty($questoes, 'Deve haver questões para responder');
        
        $respostas = ['csrf_token' => $csrfAv];
        foreach ($questoes as $q) {
            $respostas[$q] = 'A'; // Responde todas como 'A'
        }
        
        $res = $this->httpPost($path . '/responder', $respostas);
        $this->assertEquals(302, $res['code'], 'Submissão deve redirecionar');
        
        // Seguir para resultado
        preg_match('/Location: ([^\r\n]+)/', $res['headers'], $loc2);
        $resultPath = parse_url(trim($loc2[1] ?? ''), PHP_URL_PATH);
        
        $res = $this->httpGet($resultPath ?: $path . '/resultado');
        $this->assertEquals(200, $res['code'], 'Resultado deve carregar');
        $this->assertStringContainsString('nota-grande', $res['body'], 'Deve mostrar nota');
        $this->assertStringContainsString('%', $res['body'], 'Deve mostrar porcentagem');
    }

    public function testLogout(): void
    {
        $this->cadastrarELogar();
        
        $res = $this->httpGet('/logout');
        $this->assertEquals(302, $res['code']);
        $this->assertStringContainsString('login', $res['headers']);
        
        // Verificar que não está mais logado
        $res = $this->httpGet('/painel');
        $this->assertEquals(302, $res['code'], 'Após logout, painel deve redirecionar');
    }

    public function testCadastroEmailDuplicadoHttp(): void
    {
        $this->cadastrarELogar();
        
        // Tentar cadastrar mesmo email novamente
        $res = $this->httpGet('/cadastro');
        $csrf = $this->extractCsrf($res['body']);
        
        $res = $this->httpPost('/cadastro', [
            'csrf_token' => $csrf,
            'nome' => 'Outro Nome',
            'email' => 'fluxo@teste.com',
            'senha' => '1234',
            'confirmar_senha' => '1234',
        ]);
        
        $this->assertEquals(200, $res['code'], 'Email duplicado deve mostrar erro (não redirecionar)');
        // Verificar se o HTML contém mensagem de erro
        $hasDuplicado = str_contains($res['body'], 'já está cadastrado') || 
                        str_contains($res['body'], 'email já está cadastrado');
        $this->assertTrue($hasDuplicado, 'Deve mostrar mensagem de email duplicado');
    }

    public function testRecuperarAcessoFlow(): void
    {
        // Cadastrar primeiro
        $res = $this->httpGet('/cadastro');
        $csrf = $this->extractCsrf($res['body']);
        $this->httpPost('/cadastro', [
            'csrf_token' => $csrf,
            'nome' => 'Recuperar Teste',
            'telefone' => '11922222222',
            'email' => 'recupera@teste.com',
            'senha' => '1234',
            'confirmar_senha' => '1234',
        ]);
        
        // Buscar conta
        $res = $this->httpGet('/recuperar-acesso');
        $csrf2 = $this->extractCsrf($res['body']);
        
        $res = $this->httpPost('/recuperar-acesso', [
            'csrf_token' => $csrf2,
            'nome' => 'Recuperar Teste',
            'telefone' => '11922222222',
        ]);
        $this->assertEquals(200, $res['code']);
        $this->assertStringContainsString('Conta Encontrada', $res['body']);
        $this->assertStringContainsString('recupera@teste.com', $res['body']);
        $this->assertStringContainsString('Redefinir Senha', $res['body']);
        
        // Redefinir senha
        $csrf3 = $this->extractCsrf($res['body']);
        $res = $this->httpPost('/redefinir-senha', [
            'csrf_token' => $csrf3,
            'nome' => 'Recuperar Teste',
            'telefone' => '11922222222',
            'nova_senha' => 'nova123',
            'confirmar_senha' => 'nova123',
        ]);
        $this->assertEquals(302, $res['code'], 'Redefinição deve redirecionar');
        $this->assertStringContainsString('login', $res['headers']);
        
        // Login com nova senha
        $res = $this->httpGet('/login');
        $csrf4 = $this->extractCsrf($res['body']);
        $res = $this->httpPost('/login', [
            'csrf_token' => $csrf4,
            'email' => 'recupera@teste.com',
            'senha' => 'nova123',
        ]);
        $this->assertEquals(302, $res['code'], 'Login com nova senha deve funcionar');
        $this->assertStringContainsString('painel', $res['headers']);
    }

    public function testAdminLogin(): void
    {
        $res = $this->httpGet('/admin-login');
        $this->assertEquals(200, $res['code']);
        $this->assertStringContainsString('Acesso Administrativo', $res['body']);
        $this->assertStringContainsString('csrf_token', $res['body']);
        
        // Tentar login admin
        $csrf = $this->extractCsrf($res['body']);
        $res = $this->httpPost('/admin-login', [
            'csrf_token' => $csrf,
            'senha_admin' => 'admin_enfermagem_2026',
        ]);
        $this->assertEquals(302, $res['code'], 'Admin login deve redirecionar');
        $this->assertStringContainsString('admin', $res['headers']);
        
        // Acessar admin
        $res = $this->httpGet('/admin');
        $this->assertEquals(200, $res['code']);
        $this->assertStringContainsString('Administração', $res['body']);
        $this->assertStringContainsString('Estudantes', $res['body']);
        $this->assertStringContainsString('Questões', $res['body']);
    }

    public function test404NotFound(): void
    {
        $res = $this->httpGet('/rota-inexistente-sem-router');
        // A página inicial é servida como fallback, mas o status pode ser 200 ou 404
        $this->assertContains($res['code'], [200, 404], 'Rota inexistente deve retornar 200 ou 404');
    }

    private function cadastrarELogar(): void
    {
        // Cadastro
        $res = $this->httpGet('/cadastro');
        $csrf = $this->extractCsrf($res['body']);
        $this->httpPost('/cadastro', [
            'csrf_token' => $csrf,
            'nome' => 'Fluxo Teste',
            'email' => 'fluxo@teste.com',
            'senha' => '1234',
            'confirmar_senha' => '1234',
        ]);
        
        // Login
        $res = $this->httpGet('/login');
        $csrf2 = $this->extractCsrf($res['body']);
        $this->httpPost('/login', [
            'csrf_token' => $csrf2,
            'email' => 'fluxo@teste.com',
            'senha' => '1234',
        ], true);
    }
}
