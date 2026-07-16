<?php
/**
 * router.php — Roteador para o servidor PHP embutido
 * 
 * O servidor PHP built-in (php -S) não processa .htaccess.
 * Este arquivo replica as regras de reescrita do .htaccess.
 * 
 * Uso: php -S 0.0.0.0:8080 -t . router.php
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// 1. Servir arquivos estáticos diretamente
$docRoot = __DIR__;
$filePath = $docRoot . $path;

if ($path !== '/' && is_file($filePath)) {
    // Deixar o PHP servir o arquivo estático
    return false;
}

// 2. Rotas de assets/ e scripts/ — servir diretamente
if (preg_match('#^/php/assets/|^/php/scripts/|^/assets/|^/scripts/#', $path)) {
    return false;
}

// 3. Tudo mais vai para o index.php (front controller)
$_GET['url'] = ltrim($path, '/');
require $docRoot . '/index.php';
