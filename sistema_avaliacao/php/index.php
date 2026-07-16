<?php
/**
 * index.php — Front Controller / Roteador Principal
 * 
 * Sistema de Avaliação — Introdução à Enfermagem (EN_430)
 * Versão PHP + JavaScript
 * 
 * Gerencia todas as rotas da aplicação:
 *   /                     → Página inicial
 *   /cadastro             → Cadastro de estudante
 *   /login                → Login
 *   /logout               → Logout
 *   /recuperar-acesso     → Recuperação de senha
 *   /redefinir-senha      → Redefinição de senha
 *   /painel               → Dashboard do estudante
 *   /nova-avaliacao       → Gerar avaliação
 *   /avaliacao/{id}       → Responder avaliação
 *   /avaliacao/{id}/responder → Submeter respostas
 *   /avaliacao/{id}/resultado → Resultado
 *   /progresso            → Progresso do estudante
 *   /admin                → Painel administrativo
 *   /admin-login          → Login admin
 *   /admin/logout         → Logout admin
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/handlers.php';

// Inicializar sessão
initSession();

// Obter a rota
$url = $_GET['url'] ?? '';
$url = trim($url, '/');
$urlParts = explode('/', $url);
$rota = $urlParts[0] ?: 'index';
$method = $_SERVER['REQUEST_METHOD'];

// ═══════════════════════════════════════════════════════════════
// ROTEAMENTO
// ═══════════════════════════════════════════════════════════════

try {
    switch ($rota) {
        // ── Página Inicial ──
        case 'index':
        case '':
            view('index');
            break;

        // ── Cadastro ──
        case 'cadastro':
            if ($method === 'POST') {
                requireCsrf();
                handleCadastro();
            } else {
                view('cadastro');
            }
            break;

        // ── Login ──
        case 'login':
            if ($method === 'POST') {
                requireCsrf();
                handleLogin();
            } else {
                view('login');
            }
            break;

        // ── Logout ──
        case 'logout':
            session_destroy();
            flash('info', '👋 Você saiu do sistema.');
            redirect('login');
            break;

        // ── Recuperar Acesso ──
        case 'recuperar-acesso':
            if ($method === 'POST') {
                requireCsrf();
                handleRecuperarAcesso();
            } else {
                view('recuperar_acesso');
            }
            break;

        // ── Redefinir Senha ──
        case 'redefinir-senha':
            if ($method === 'POST') {
                requireCsrf();
                handleRedefinirSenha();
            } else {
                redirect('recuperar-acesso');
            }
            break;

        // ── Painel ──
        case 'painel':
            requireLogin();
            handlePainel();
            break;

        // ── Nova Avaliação ──
        case 'nova-avaliacao':
            requireLogin();
            if ($method === 'POST') {
                requireCsrf();
                handleNovaAvaliacao();
            } else {
                redirect('painel');
            }
            break;

        // ── Avaliação ──
        case 'avaliacao':
            requireLogin();
            $avaliacaoId = (int)($urlParts[1] ?? 0);
            $subRota = $urlParts[2] ?? '';
            
            if ($subRota === 'responder') {
                if ($method === 'POST') {
                    requireCsrf();
                    handleSubmeterRespostas($avaliacaoId);
                } else {
                    redirect("avaliacao/{$avaliacaoId}");
                }
            } elseif ($subRota === 'resultado') {
                handleResultado($avaliacaoId);
            } else {
                handleResponderAvaliacao($avaliacaoId);
            }
            break;

        // ── Progresso ──
        case 'progresso':
            requireLogin();
            handleProgresso();
            break;

        // ── Admin ──
        case 'admin':
            if (!empty($_GET['logout_admin'])) {
                session_unset();
                $_SESSION = [];
                flash('info', '🔒 Você saiu do painel administrativo.');
                redirect('admin-login');
            }
            handleAdmin();
            break;

        case 'admin-login':
            if ($method === 'POST') {
                requireCsrf();
                handleAdminLogin();
            } else {
                view('admin_login');
            }
            break;

        // ── 404 ──
        default:
            http_response_code(404);
            view('index', ['erro404' => true]);
            break;
    }
} catch (Exception $e) {
    if (DEBUG) {
        die("Erro: " . $e->getMessage());
    }
    flash('erro', '❌ Erro interno do servidor. Tente novamente.');
    redirect('painel');
}

