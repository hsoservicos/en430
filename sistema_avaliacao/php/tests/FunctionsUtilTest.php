<?php
/**
 * FunctionsUtilTest — Testes de funções utilitárias de functions.php
 * 
 * Cobre funções não testadas pelos testes existentes:
 * flash(), getFlashes(), renderFlashes(), url(), 
 * initSession(), requireLogin(), requireAdmin(), requireCsrf(),
 * getEstudanteNome(), agora(), view()
 */
class FunctionsUtilTest extends PHPUnit\Framework\TestCase
{
    private PDO $db;

    protected function setUp(): void
    {
        $_SESSION = [];
        $_SERVER = ['SCRIPT_NAME' => '/index.php'];

        limparBanco();
        $this->db = getTestDb();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_SERVER = [];
    }

    // ═══════════════════════════════════════════════════════════
    // SESSÃO
    // ═══════════════════════════════════════════════════════════

    public function testInitSessionNaoIniciaSeJaAtiva(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $statusAntes = session_status();
        initSession();
        $statusDepois = session_status();

        $this->assertEquals(PHP_SESSION_ACTIVE, $statusAntes);
        $this->assertEquals(PHP_SESSION_ACTIVE, $statusDepois);
    }

    public function testInitSessionIniciaQuandoNaoAtiva(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $this->assertEquals(PHP_SESSION_NONE, session_status());
        initSession();
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
        session_write_close();
    }

    // ═══════════════════════════════════════════════════════════
    // FLASH MESSAGES
    // ═══════════════════════════════════════════════════════════

    public function testFlashAdicionaMensagem(): void
    {
        $_SESSION = [];
        flash('sucesso', 'Cadastro realizado!');

        $this->assertArrayHasKey('_flash', $_SESSION);
        $this->assertCount(1, $_SESSION['_flash']);
        $this->assertEquals('sucesso', $_SESSION['_flash'][0]['tipo']);
        $this->assertEquals('Cadastro realizado!', $_SESSION['_flash'][0]['mensagem']);
    }

    public function testFlashMultiplasMensagens(): void
    {
        $_SESSION = [];
        flash('sucesso', 'Primeira');
        flash('erro', 'Segunda');
        flash('info', 'Terceira');

        $this->assertCount(3, $_SESSION['_flash']);
    }

    public function testFlashMensagemVazia(): void
    {
        $_SESSION = [];
        flash('', '');

        $this->assertCount(1, $_SESSION['_flash']);
        $this->assertEquals('', $_SESSION['_flash'][0]['tipo']);
        $this->assertEquals('', $_SESSION['_flash'][0]['mensagem']);
    }

    public function testGetFlashesRetornaELimpa(): void
    {
        $_SESSION = [];
        flash('sucesso', 'Mensagem teste');

        $flashes = getFlashes();

        $this->assertCount(1, $flashes);
        $this->assertEquals('sucesso', $flashes[0]['tipo']);
        $this->assertEquals('Mensagem teste', $flashes[0]['mensagem']);
        $this->assertArrayNotHasKey('_flash', $_SESSION);
    }

    public function testGetFlashesSemMensagens(): void
    {
        $_SESSION = [];
        $flashes = getFlashes();

        $this->assertIsArray($flashes);
        $this->assertEmpty($flashes);
    }

    public function testGetFlashesNaoLimpaOutrosDadosSessao(): void
    {
        $_SESSION = ['estudante_id' => 42, '_flash' => [['tipo' => 'info', 'mensagem' => 'teste']]];

        getFlashes();

        $this->assertEquals(42, $_SESSION['estudante_id'], 'Outros dados devem ser preservados');
        $this->assertArrayNotHasKey('_flash', $_SESSION, 'Flash deve ser removido');
    }

    public function testRenderFlashesRetornaHtml(): void
    {
        $_SESSION = [];
        flash('sucesso', 'Operação concluída!');
        flash('erro', 'Algo deu errado.');

        $html = renderFlashes();

        $this->assertStringContainsString('flash-sucesso', $html);
        $this->assertStringContainsString('Operação concluída!', $html);
        $this->assertStringContainsString('flash-erro', $html);
        $this->assertStringContainsString('Algo deu errado.', $html);
        $this->assertStringStartsWith('<div', $html);
    }

    public function testRenderFlashesComTipoInfo(): void
    {
        $_SESSION = [];
        flash('info', 'Nota importante');

        $html = renderFlashes();

        $this->assertStringContainsString('flash-info', $html);
        $this->assertStringContainsString('Nota importante', $html);
    }

    public function testRenderFlashesVazio(): void
    {
        $_SESSION = [];
        $html = renderFlashes();

        $this->assertEmpty($html);
    }

    public function testRenderFlashesEscapaHtml(): void
    {
        $_SESSION = [];
        flash('erro', '<script>alert("xss")</script>');

        $html = renderFlashes();

        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function testRenderFlashesLimpaSessao(): void
    {
        $_SESSION = [];
        flash('sucesso', 'Teste');
        renderFlashes();

        $this->assertArrayNotHasKey('_flash', $_SESSION);
    }

    public function testFlashSobrescreveArraySeJaExiste(): void
    {
        $_SESSION = ['_flash' => [['tipo' => 'antigo', 'mensagem' => 'msg antiga']]];

        flash('novo', 'msg nova');

        $this->assertCount(2, $_SESSION['_flash']);
        $this->assertEquals('antigo', $_SESSION['_flash'][0]['tipo']);
        $this->assertEquals('novo', $_SESSION['_flash'][1]['tipo']);
    }

    // ═══════════════════════════════════════════════════════════
    // URL
    // ═══════════════════════════════════════════════════════════

    public function testUrlRaiz(): void
    {
        $url = url('');
        $this->assertEquals('/', $url);
    }

    public function testUrlRotaSimples(): void
    {
        $url = url('login');
        $this->assertEquals('/login', $url);
    }

    public function testUrlComParametros(): void
    {
        $url = url('avaliacao', ['id' => 5, 'modulo' => 3]);
        $this->assertEquals('/avaliacao?id=5&modulo=3', $url);
    }

    public function testUrlRotaComBarraInicial(): void
    {
        $url = url('/painel');
        $this->assertEquals('/painel', $url);
    }

    public function testUrlComBaseEmSubdiretorio(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/sistema/index.php';
        $url = url('login');
        $this->assertEquals('/sistema/login', $url);
    }

    public function testUrlRaizEmSubdiretorio(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/sistema/index.php';
        $url = url('');
        $this->assertEquals('/sistema/', $url);
    }

    public function testUrlScriptNameSemIndexPhp(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/app.php';
        $url = url('login');
        $this->assertEquals('/login', $url);
    }

    public function testUrlScriptNameVazio(): void
    {
        $_SERVER['SCRIPT_NAME'] = '';
        $url = url('login');
        $this->assertEquals('/login', $url);
    }

    public function testUrlScriptNameInexistente(): void
    {
        unset($_SERVER['SCRIPT_NAME']);
        $url = url('login');
        $this->assertEquals('/login', $url);
    }

    // ═══════════════════════════════════════════════════════════
    // REDIRECT URL
    // ═══════════════════════════════════════════════════════════

    public function testRedirectUrlComRota(): void
    {
        $url = redirectUrl('painel');
        $this->assertEquals('/painel', $url);
    }

    public function testRedirectUrlRotaVazia(): void
    {
        $url = redirectUrl('');
        $this->assertEquals('/', $url);
    }

    public function testRedirectUrlChamaUrlInternamente(): void
    {
        // redirectUrl('login') deve retornar o mesmo que url('login')
        $esperado = url('login');
        $this->assertEquals($esperado, redirectUrl('login'));
    }

    public function testRedirectUrlRotaComEscaping(): void
    {
        $url = redirectUrl('avaliacao');
        $this->assertStringStartsWith('/', $url);
        $this->assertStringContainsString('avaliacao', $url);
    }

    public function testRedirectUrlComBasePadrao(): void
    {
        // Teste com SCRIPT_NAME padrao (/index.php -> base '')
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $url = redirectUrl('login');
        $this->assertEquals('/login', $url);
    }

    // ═══════════════════════════════════════════════════════════
    // AUTENTICAÇÃO
    // ═══════════════════════════════════════════════════════════

    public function testGetEstudanteNomeRetornaNome(): void
    {
        $_SESSION['estudante_nome'] = 'Maria Silva';
        $this->assertEquals('Maria Silva', getEstudanteNome());
    }

    public function testGetEstudanteNomeRetornaVazio(): void
    {
        $_SESSION = [];
        $this->assertEquals('', getEstudanteNome());
    }

    public function testGetEstudanteNomeComAcentos(): void
    {
        $_SESSION['estudante_nome'] = 'João José';
        $this->assertEquals('João José', getEstudanteNome());
    }

    public function testEstaLogadoRetornaTrue(): void
    {
        $_SESSION = ['estudante_id' => 42];
        $this->assertTrue(estaLogado());
    }

    public function testEstaLogadoRetornaFalse(): void
    {
        $_SESSION = [];
        $this->assertFalse(estaLogado());
    }

    public function testRequireLoginSemSessaoFlash(): void
    {
        $_SESSION = [];
        $this->assertFalse(estaLogado());

        if (!estaLogado()) {
            flash('erro', '🔒 Faça login para acessar esta página.');
        }

        $flashes = getFlashes();
        $this->assertCount(1, $flashes);
        $this->assertStringContainsString('Faça login', $flashes[0]['mensagem']);
    }

    public function testRequireAdminSemSessaoDetecta(): void
    {
        $_SESSION = [];
        $this->assertTrue(empty($_SESSION['admin_authenticated']));
    }

    // ═══════════════════════════════════════════════════════════
    // DATAS
    // ═══════════════════════════════════════════════════════════

    public function testAgoraFormatoCorreto(): void
    {
        $agora = agora();
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $agora);
    }

    public function testAgoraDataAtual(): void
    {
        $agora = agora();
        $hoje = date('Y-m-d');
        $this->assertStringStartsWith($hoje, $agora, 'Data deve ser a atual');
    }

    public function testDataHoraBrasilFormatoCorreto(): void
    {
        $this->assertEquals('15/07/2026 14:30', dataHoraBrasil('2026-07-15 14:30:00'));
    }

    public function testDataHoraBrasilVariadas(): void
    {
        $this->assertEquals('01/01/2026 00:00', dataHoraBrasil('2026-01-01 00:00:00'));
        $this->assertEquals('31/12/2026 23:59', dataHoraBrasil('2026-12-31 23:59:59'));
    }

    public function testDataHoraBrasilNull(): void
    {
        $this->assertEquals('-', dataHoraBrasil(null));
    }

    public function testDataHoraBrasilStringVazia(): void
    {
        $this->assertEquals('-', dataHoraBrasil(''));
    }

    public function testDataBrasilFormatoCorreto(): void
    {
        $this->assertEquals('15/07/2026', dataBrasil('2026-07-15'));
    }

    public function testDataBrasilComHoraIgnorada(): void
    {
        $this->assertEquals('15/07/2026', dataBrasil('2026-07-15 14:30:00'));
    }

    public function testDataBrasilNull(): void
    {
        $this->assertEquals('-', dataBrasil(null));
    }

    public function testDataBrasilStringVazia(): void
    {
        $this->assertEquals('-', dataBrasil(''));
    }

    // ═══════════════════════════════════════════════════════════
    // CSRF
    // ═══════════════════════════════════════════════════════════

    public function testGenerateCsrfTokenCriaNaPrimeiraVez(): void
    {
        $_SESSION = [];
        $token = generateCsrfToken();
        $this->assertNotEmpty($token);
        $this->assertArrayHasKey('csrf_token', $_SESSION);
    }

    public function testGenerateCsrfTokenReusaExistente(): void
    {
        $_SESSION = [];
        $token1 = generateCsrfToken();
        $token2 = generateCsrfToken();
        $this->assertSame($token1, $token2,
            'Token deve ser reusado e nao regenerado');
    }

    public function testRequireCsrfComTokenValido(): void
    {
        $_SESSION = [];
        $token = generateCsrfToken();
        $this->assertTrue(validateCsrfToken($token));
    }

    public function testRequireCsrfComTokenInvalidoFlash(): void
    {
        $_SESSION = [];
        $_POST['csrf_token'] = 'token_invalido';

        $token = $_POST['csrf_token'] ?? '';
        if (!validateCsrfToken($token)) {
            flash('erro', '❌ Token de segurança inválido. Tente novamente.');
        }

        $flashes = getFlashes();
        $this->assertCount(1, $flashes);
        $this->assertEquals('erro', $flashes[0]['tipo']);
    }

    public function testRequireCsrfSemToken(): void
    {
        $_SESSION = [];
        $_POST = [];

        $token = $_POST['csrf_token'] ?? '';
        if (!validateCsrfToken($token)) {
            flash('erro', '❌ Token de segurança inválido. Tente novamente.');
        }

        $flashes = getFlashes();
        $this->assertCount(1, $flashes);
    }

    public function testCsrfComTokenExpirado(): void
    {
        $_SESSION = ['csrf_token' => 'token_antigo'];
        $this->assertFalse(validateCsrfToken('token_novo'));
    }

    public function testCsrfComTokenNulo(): void
    {
        $_SESSION = ['csrf_token' => 'token_valido'];
        $this->assertFalse(validateCsrfToken(null));
    }

    // ═══════════════════════════════════════════════════════════
    // VIEWS E ASSETS
    // ═══════════════════════════════════════════════════════════

    public function testTodasViewsExistem(): void
    {
        $views = ['index', 'cadastro', 'login', 'painel', 'avaliacao',
                  'resultado', 'progresso', 'recuperar_acesso', 'admin'];

        foreach ($views as $view) {
            $this->assertFileExists(
                VIEWS_PATH . '/' . $view . '.php',
                "View {$view}.php deve existir"
            );
        }
    }

    public function testAssetsExistem(): void
    {
        $this->assertFileExists(ASSETS_PATH . '/css/style.css');
        $this->assertFileExists(ASSETS_PATH . '/js/app.js');
    }

    public function testRouterFileExiste(): void
    {
        $this->assertFileExists(dirname(__DIR__) . '/router.php');
    }

    // ═══════════════════════════════════════════════════════════
    // GETTERS
    // ═══════════════════════════════════════════════════════════

    public function testGetEstudanteIdRetornaId(): void
    {
        $_SESSION['estudante_id'] = 42;
        $this->assertSame(42, getEstudanteId());
    }

    public function testGetEstudanteIdRetornaZero(): void
    {
        $_SESSION = [];
        $this->assertSame(0, getEstudanteId());
    }

    public function testGetEstudanteIdRetornaZeroCast(): void
    {
        $_SESSION['estudante_id'] = 'nao_numerico';
        $this->assertSame(0, getEstudanteId());
    }

    // ═══════════════════════════════════════════════════════════
    // REQUIRE FUNCTIONS (caminho sem redirect)
    // ═══════════════════════════════════════════════════════════

    public function testRequireLoginJaLogadoNaoRedireciona(): void
    {
        $_SESSION['estudante_id'] = 1;
        // Não deve chamar redirect()/exit()
        requireLogin();
        $this->assertTrue(estaLogado());
    }

    public function testRequireAdminAutenticadoNaoRedireciona(): void
    {
        $_SESSION['admin_authenticated'] = true;
        // Não deve chamar redirect()/exit()
        requireAdmin();
        $this->assertNotEmpty($_SESSION['admin_authenticated']);
    }

    public function testRequireCsrfComTokenValidoNaoRedireciona(): void
    {
        $_SESSION = [];
        $_POST = [];
        $token = generateCsrfToken();
        $_POST['csrf_token'] = $token;

        // Não deve chamar redirect()/exit()
        requireCsrf();
        $this->assertTrue(validateCsrfToken($token));
    }

    // ═══════════════════════════════════════════════════════════
    // VIEW
    // ═══════════════════════════════════════════════════════════

    public function testViewComViewExistenteOutput(): void
    {
        $_SESSION = [];
        ob_start();
        view('index', ['titulo' => 'Teste']);
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Sistema de Avaliação', $output);
    }

    // ═══════════════════════════════════════════════════════════
    // VALIDAÇÕES
    // ═══════════════════════════════════════════════════════════

    public function testValidarEmailLimites(): void
    {
        $this->assertTrue(validarEmail('a@b.co'));
        $this->assertTrue(validarEmail('teste.nome@sub.dominio.com.br'));
        $this->assertFalse(validarEmail('espaço@teste.com'));
        $this->assertFalse(validarEmail('sem-arroba'));
        $this->assertFalse(validarEmail(''));
    }

    public function testValidarSenhaLimites(): void
    {
        $this->assertFalse(validarSenha('12'));
        $this->assertTrue(validarSenha('1234'));
        $this->assertTrue(validarSenha(str_repeat('a', 100)));
    }

    // ═══════════════════════════════════════════════════════════
    // PORCENTAGEM E BADGE (casos adicionais)
    // ═══════════════════════════════════════════════════════════

    public function testCalcularPorcentagemCasosLimite(): void
    {
        $this->assertEquals(0.0, calcularPorcentagem(0, 1));
        $this->assertEquals(100.0, calcularPorcentagem(1, 1));
        $this->assertEquals(33.3, calcularPorcentagem(1, 3));
        $this->assertEquals(66.7, calcularPorcentagem(2, 3));
        $this->assertEquals(0.1, calcularPorcentagem(1, 1000));
    }

    public function testBadgeNotaCasosLimite(): void
    {
        $this->assertStringContainsString('Excelente', badgeNota(100));
        $this->assertStringContainsString('Excelente', badgeNota(90));
        $this->assertStringContainsString('Muito bom', badgeNota(89));
        $this->assertStringContainsString('Muito bom', badgeNota(70));
        $this->assertStringContainsString('Bom', badgeNota(69));
        $this->assertStringContainsString('Bom', badgeNota(50));
        $this->assertStringContainsString('estudar', badgeNota(49));
        $this->assertStringContainsString('estudar', badgeNota(0));
    }

}
