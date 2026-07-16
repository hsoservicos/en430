<?php
/**
 * config.php — Configuração da Aplicação
 * Sistema de Avaliação — Introdução à Enfermagem (EN_430)
 * 
 * Versão PHP + JavaScript
 */

// ─── Caminhos ───────────────────────────────────────────────
define('BASE_PATH', __DIR__);
define('DB_PATH', BASE_PATH . '/avaliacao.db');
define('VIEWS_PATH', BASE_PATH . '/views');
define('ASSETS_PATH', BASE_PATH . '/assets');
define('QUESTOES_POR_AVALIACAO', 20);

// ─── Segurança ──────────────────────────────────────────────
define('SECRET_KEY', getenv('SECRET_KEY') ?: 'enfermagem_en430_secret_key_2026');
define('ADMIN_SECRET', getenv('ADMIN_SECRET') ?: 'admin_enfermagem_2026');

// ─── Sessão ─────────────────────────────────────────────────
define('SESSION_LIFETIME', 86400); // 24 horas

// ─── Modo Debug ─────────────────────────────────────────────
define('DEBUG', getenv('APP_DEBUG') === '1' || getenv('APP_DEBUG') === 'true');
