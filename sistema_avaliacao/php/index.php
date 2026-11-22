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
 *   /politica-de-privacidade → Política de Privacidade (pública)
 *   /excluir-conta        → Exclusão da própria conta (LGPD)
 *   /reconsentimento      → Aceite da versão vigente da política
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

        // ── Reconsentimento (política em nova versão) ──
        case 'reconsentimento':
            requireLogin();
            if ($method === 'POST') {
                requireCsrf();
                handleReconsentimento();
            } elseif (!precisaReconsentimento()) {
                redirect('painel');
            } else {
                view('reconsentimento', ['versao_anterior' => getConsentimentoVersaoEstudante()]);
            }
            break;

        // ── Painel ──
        case 'painel':
            requireLogin();
            if (!exigirConsentimentoAtual()) {
                break;
            }
            handlePainel();
            break;

        // ── Nova Avaliação ──
        case 'nova-avaliacao':
            requireLogin();
            if (!exigirConsentimentoAtual()) {
                break;
            }
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
            if (!exigirConsentimentoAtual()) {
                break;
            }
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
            if (!exigirConsentimentoAtual()) {
                break;
            }
            handleProgresso();
            break;

        // ── Política de Privacidade (pública) ──
        case 'politica-de-privacidade':
            $markdown = getPoliticaMarkdown();
            if ($markdown === '') {
                if (DEBUG) {
                    die('Política de Privacidade não encontrada em ' . htmlspecialchars(POLITICA_MD_PATH));
                }
                http_response_code(500);
                view('erro', ['codigo' => 500]);
                break;
            }
            view('politica_privacidade', [
                'conteudo' => renderMarkdownSimples($markdown),
                'versao' => getPoliticaVersao($markdown) ?? CONSENTIMENTO_VERSAO,
            ]);
            break;

        // ── Exclusão da própria conta (LGPD) ──
        case 'excluir-conta':
            requireLogin();
            if ($method === 'POST') {
                requireCsrf();
                handleExcluirConta();
            } else {
                view('excluir_conta');
            }
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

        // ── Admin: CRUD Estudantes ──
        case 'admin-excluir-estudante':
            requireAdmin();
            $id = (int)($urlParts[1] ?? 0);
            handleAdminExcluirEstudante($id);
            break;

        case 'admin-resetar-senha':
            requireAdmin();
            $id = (int)($urlParts[1] ?? 0);
            handleAdminResetarSenha($id);
            break;

        // ── Admin: Exportar CSV ──
        case 'admin-exportar':
            requireAdmin();
            $tipo = $urlParts[1] ?? '';
            handleAdminExportarCSV($tipo);
            break;

        // ── Admin: Filtrar Estudantes ──
        case 'admin-filtrar':
            requireAdmin();
            handleAdminFiltrarEstudantes();
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
            view('erro', ['codigo' => 404]);
            break;
    }
} catch (Exception $e) {
    if (DEBUG) {
        die("Erro: " . htmlspecialchars($e->getMessage()));
    }
    http_response_code(500);
    view('erro', ['codigo' => 500]);
}

