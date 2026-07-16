<?php
/**
 * functions.php — Funções auxiliares
 * Autenticação, CSRF, Flash Messages, Utilitários
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// ═══════════════════════════════════════════════════════════════
// SESSÃO
// ═══════════════════════════════════════════════════════════════

function initSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => $secure,
        ]);
        session_start();
    }
}

// ═══════════════════════════════════════════════════════════════
// CSRF PROTECTION
// ═══════════════════════════════════════════════════════════════

function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function getCsrfField(): string {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

function validateCsrfToken(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function requireCsrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        flash('erro', '❌ Token de segurança inválido. Tente novamente.');
        redirect(isset($_SESSION['estudante_id']) ? 'painel' : 'login');
    }
}

// ═══════════════════════════════════════════════════════════════
// FLASH MESSAGES
// ═══════════════════════════════════════════════════════════════

function flash(string $tipo, string $mensagem): void {
    if (!isset($_SESSION['_flash'])) {
        $_SESSION['_flash'] = [];
    }
    $_SESSION['_flash'][] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

function getFlashes(): array {
    $flashes = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $flashes;
}

function renderFlashes(): string {
    $html = '';
    foreach (getFlashes() as $f) {
        $tipo = htmlspecialchars($f['tipo']);
        $msg = htmlspecialchars($f['mensagem']);
        $html .= "<div class=\"flash flash-{$tipo}\">{$msg}</div>";
    }
    return $html;
}

// ═══════════════════════════════════════════════════════════════
// RATE LIMITING (LOGIN)
// ═══════════════════════════════════════════════════════════════

/**
 * Verifica se o número de tentativas de login excedeu o limite.
 * Armazena as tentativas na sessão, indexadas pelo IP do cliente.
 * 
 * @return bool True se o limite foi excedido (bloqueado), False se pode tentar
 */
function checkLoginAttempts(): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $key = '_login_attempts_' . md5($ip);
    
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'first_attempt' => 0];
    
    // Se passou do tempo de bloqueio, resetar
    if (time() - $attempts['first_attempt'] > LOGIN_TIMEOUT) {
        $attempts = ['count' => 0, 'first_attempt' => 0];
        $_SESSION[$key] = $attempts;
    }
    
    return $attempts['count'] >= LOGIN_MAX_ATTEMPTS;
}

/**
 * Registra uma tentativa de login (bem-sucedida ou falha).
 * Reseta o contador em caso de sucesso.
 * 
 * @param bool $sucesso Se true, reseta as tentativas
 */
function recordLoginAttempt(bool $sucesso = false): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $key = '_login_attempts_' . md5($ip);
    
    if ($sucesso) {
        // Login bem-sucedido: resetar tentativas
        unset($_SESSION[$key]);
        return;
    }
    
    // Login falhou: incrementar contador
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'first_attempt' => time()];
    
    if ($attempts['count'] === 0) {
        $attempts['first_attempt'] = time();
    }
    
    $attempts['count']++;
    $_SESSION[$key] = $attempts;
}

/**
 * Retorna o tempo restante de bloqueio em segundos.
 * 
 * @return int Segundos restantes (0 se não estiver bloqueado)
 */
function getLoginBlockTime(): int {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $key = '_login_attempts_' . md5($ip);
    
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'first_attempt' => 0];
    
    if ($attempts['count'] < LOGIN_MAX_ATTEMPTS) {
        return 0;
    }
    
    $remaining = LOGIN_TIMEOUT - (time() - $attempts['first_attempt']);
    return max(0, $remaining);
}

// ═══════════════════════════════════════════════════════════════
// AUTENTICAÇÃO
// ═══════════════════════════════════════════════════════════════

function hashSenha(string $senha): string {
    return password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verificarSenha(string $senha, string $hash): bool {
    return password_verify($senha, $hash);
}

function estaLogado(): bool {
    return !empty($_SESSION['estudante_id']);
}

function requireLogin(): void {
    if (!estaLogado()) {
        flash('erro', '🔒 Faça login para acessar esta página.');
        redirect('login');
        return; // Segurança: redirect pode retornar em modo de teste
    }
}

function requireAdmin(): void {
    if (empty($_SESSION['admin_authenticated'])) {
        redirect('admin-login');
        return; // Segurança: redirect pode retornar em modo de teste
    }
}

function getEstudanteId(): int {
    return (int)($_SESSION['estudante_id'] ?? 0);
}

function getEstudanteNome(): string {
    return $_SESSION['estudante_nome'] ?? '';
}

// ═══════════════════════════════════════════════════════════════
// REDIRECT E URL
// ═══════════════════════════════════════════════════════════════

function redirectUrl(string $rota = ''): string {
    return $rota ? url($rota) : url('');
}

function redirect(string $rota = ''): void {
    // Em testes de unidade (PHPUNIT_TEST definido), não chamar exit()
    // para permitir que as asserções sejam executadas.
    if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
        $_SESSION['_test_redirect'] = $rota;
        return;
    }
    header("Location: " . redirectUrl($rota));
    exit;
}

function url(string $rota = '', array $params = []): string {
    // Detectar caminho base automaticamente
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    if (str_ends_with($scriptName, 'index.php')) {
        $base = dirname($scriptName);
    } else {
        $base = '';
    }
    // Normalizar: garantir que termine SEM barra
    $base = rtrim($base, '/');
    
    $rota = ltrim($rota, '/');
    $url = $base ? $base . '/' . $rota : '/' . $rota;
    
    if (empty($rota)) {
        $url = $base ? $base . '/' : '/';
    }
    
    if (!empty($params)) {
        $query = http_build_query($params);
        $url .= (str_contains($url, '?') ? '&' : '?') . $query;
    }
    
    return $url;
}

// ═══════════════════════════════════════════════════════════════
// RENDERIZAÇÃO DE VIEWS
// ═══════════════════════════════════════════════════════════════

function view(string $view, array $data = []): void {
    $viewFile = VIEWS_PATH . '/' . $view . '.php';
    
    if (!file_exists($viewFile)) {
        if (DEBUG) {
            die("View não encontrada: " . htmlspecialchars($view));
        }
        http_response_code(404);
        die("Página não encontrada.");
    }
    
    // Extrair variáveis para o escopo da view
    extract($data);
    
    // Renderizar flashes
    $flashes = renderFlashes();
    
    require $viewFile;
}

// ═══════════════════════════════════════════════════════════════
// VALIDAÇÃO
// ═══════════════════════════════════════════════════════════════

function validarEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validarSenha(string $senha): bool {
    return strlen($senha) >= 4;
}

// ═══════════════════════════════════════════════════════════════
// DATAS
// ═══════════════════════════════════════════════════════════════

function agora(): string {
    return date('Y-m-d H:i:s');
}

function dataHoraBrasil(?string $data): string {
    if (!$data) return '-';
    $dt = new DateTime($data);
    return $dt->format('d/m/Y H:i');
}

function dataBrasil(?string $data): string {
    if (!$data) return '-';
    $dt = new DateTime($data);
    return $dt->format('d/m/Y');
}

// ═══════════════════════════════════════════════════════════════
// ESTATÍSTICAS
// ═══════════════════════════════════════════════════════════════

function calcularPorcentagem(int $parte, int $total): float {
    if ($total <= 0) return 0;
    return round($parte * 100 / $total, 1);
}

function badgeNota(float $pct): string {
    if ($pct >= 90) return '🌟 Excelente!';
    if ($pct >= 70) return '✅ Muito bom!';
    if ($pct >= 50) return '📚 Bom!';
    return '📖 Precisa estudar mais!';
}
