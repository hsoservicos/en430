<?php
/**
 * router.php — Roteador para o servidor PHP embutido (php -S)
 *
 * Simula o mod_rewrite do Apache: se o arquivo/pasta requisitado não existe
 * fisicamente, redireciona para index.php preservando a URL original.
 *
 * Uso: php -S 0.0.0.0:8080 router.php
 */

$uri = $_SERVER['REQUEST_URI'];
$parsed = parse_url($uri);
$path = $parsed['path'] ?? '/';
$query = $parsed['query'] ?? '';

// Se o arquivo ou diretório físico existe, serve diretamente (assets estáticos)
$filePath = __DIR__ . $path;
if ($path !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false; // Deixa o servidor embutido servir o arquivo
}

// Rewrite: preserva parâmetros da query string original e define rota
$_SERVER['SCRIPT_NAME'] = '/index.php';
if ($query) {
    parse_str($query, $_GET);
} else {
    $_GET = [];
}
$_GET['url'] = ltrim($path, '/');

// Inclui o index.php com o contexto correto
chdir(__DIR__);
require __DIR__ . '/index.php';
