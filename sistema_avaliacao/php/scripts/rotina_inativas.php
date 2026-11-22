#!/usr/bin/env php
<?php
/**
 * rotina_inativas.php — Retenção de contas inativas (LGPD, Política item 8.2)
 *
 * Uso:
 *   php scripts/rotina_inativas.php            # avisa e exclui (produção)
 *   php scripts/rotina_inativas.php --dry-run  # só simula, não altera nada
 *   php scripts/rotina_inativas.php --ajuda
 *
 * Agende no host/Coolify uma vez por dia, por exemplo:
 *   0 4 * * * docker exec <container> php /var/www/html/scripts/rotina_inativas.php
 *
 * Com MAIL_TRANSPORT=log (padrão) nenhum e-mail é entregue e, por segurança,
 * nenhuma conta é excluída — a rotina apenas registra o que faria.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Este script só pode ser executado pela linha de comando.\n");
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../handlers.php';

$argumentos = array_slice($argv, 1);

if (in_array('--ajuda', $argumentos, true) || in_array('-h', $argumentos, true)) {
    echo "Rotina de contas inativas — EN_430\n\n"
        . "  --dry-run   Simula a execução (não envia, não altera, não exclui)\n"
        . "  --ajuda     Mostra esta ajuda\n\n"
        . "Parâmetros atuais:\n"
        . "  INATIVIDADE_MESES      = " . INATIVIDADE_MESES . "\n"
        . "  INATIVIDADE_GRACA_DIAS = " . INATIVIDADE_GRACA_DIAS . "\n"
        . "  MAIL_TRANSPORT         = " . MAIL_TRANSPORT . "\n\n";
    exit(0);
}

$dryRun = in_array('--dry-run', $argumentos, true);
$inicio = microtime(true);

echo "⏳ Rotina de contas inativas" . ($dryRun ? ' (dry-run)' : '') . "\n";
echo "   Inatividade: " . INATIVIDADE_MESES . " meses · carência: " . INATIVIDADE_GRACA_DIAS . " dias\n";
echo "   Transporte de e-mail: " . MAIL_TRANSPORT . "\n\n";

try {
    $resumo = processarRotinaInatividade($dryRun);
} catch (Throwable $e) {
    fwrite(STDERR, "❌ Falha na rotina: " . $e->getMessage() . "\n");
    exit(1);
}

echo "🔄 Reativadas (voltaram a acessar): {$resumo['reativadas']}\n";
echo "📧 Avisos: {$resumo['avisos']['enviados']} enviado(s), {$resumo['avisos']['falhas']} falha(s) de {$resumo['avisos']['candidatos']} candidato(s)\n";
echo "🗑️  Exclusões: {$resumo['exclusoes']['excluidos']} de {$resumo['exclusoes']['candidatos']} candidato(s)\n";

if (!empty($resumo['detalhes'])) {
    echo "\nDetalhes:\n";
    foreach ($resumo['detalhes'] as $detalhe) {
        $extra = isset($detalhe['status']) ? " ({$detalhe['status']})" : '';
        echo "   - {$detalhe['acao']}: conta #{$detalhe['id']}{$extra}\n";
    }
}

if ($dryRun) {
    echo "\nℹ️  Dry-run: nada foi enviado, alterado ou excluído.\n";
} elseif (MAIL_TRANSPORT !== 'mail') {
    echo "\n⚠️  MAIL_TRANSPORT=" . MAIL_TRANSPORT . ": avisos apenas registrados; nenhuma conta é excluída sem aviso entregue.\n";
}

echo "\n✅ Concluído em " . round(microtime(true) - $inicio, 2) . "s\n";
exit(0);
