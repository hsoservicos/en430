<?php
/**
 * inatividade.php — Retenção de contas inativas (LGPD, Política de Privacidade 8.2)
 *
 * Regra: conta sem acesso por INATIVIDADE_MESES (24) recebe aviso por e-mail e,
 * se continuar inativa por INATIVIDADE_GRACA_DIAS (30), é excluída com as
 * avaliações. Contas que voltam a acessar saem da rotina automaticamente.
 *
 * Segurança: a exclusão só acontece quando existe aviso REALMENTE entregue
 * (status "enviado"). Com MAIL_TRANSPORT=log o envio é apenas registrado e a
 * rotina nunca apaga contas — evita excluir dado pessoal sem aviso efetivo.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

/**
 * Registra o acesso do estudante (usado no login).
 */
function registrarAcesso(int $estudanteId): void {
    if ($estudanteId <= 0) {
        return;
    }
    try {
        $stmt = getDB()->prepare("UPDATE estudantes SET ultimo_acesso_em = datetime('now') WHERE id = ?");
        $stmt->execute([$estudanteId]);
    } catch (PDOException $e) {
        if (DEBUG) {
            error_log('Falha ao registrar acesso: ' . $e->getMessage());
        }
    }
}

/**
 * Garante a tabela de histórico de notificações (criada sob demanda).
 */
function ensureNotificacoesTable(PDO $db): void {
    $db->exec("CREATE TABLE IF NOT EXISTS notificacoes_email (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        destinatario TEXT NOT NULL,
        assunto TEXT NOT NULL,
        corpo TEXT,
        transporte TEXT NOT NULL,
        status TEXT NOT NULL,
        erro TEXT,
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

/**
 * Envia (ou apenas registra) um e-mail e grava o histórico.
 *
 * @return array{ok: bool, status: string, erro: ?string, transporte: string}
 */
function enviarEmail(string $para, string $assunto, string $corpo, ?string $transporte = null): array {
    $transporte = $transporte ?? MAIL_TRANSPORT;
    $assunto = str_replace(["\r", "\n"], ' ', $assunto);
    $erro = null;
    $status = 'erro';

    if (!validarEmail($para)) {
        $erro = 'Destinatário inválido';
    } elseif ($transporte === 'mail') {
        $headers = "From: " . MAIL_FROM . "\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n"
            . "MIME-Version: 1.0\r\n";
        $enviado = @mail($para, $assunto, $corpo, $headers);
        $status = $enviado ? 'enviado' : 'erro';
        if (!$enviado) {
            $erro = 'mail() retornou falha';
        }
    } else {
        // Transporte padrão: registra sem entregar (nenhum MTA configurado).
        $status = 'simulado';
    }

    try {
        $db = getDB();
        ensureNotificacoesTable($db);
        $stmt = $db->prepare("INSERT INTO notificacoes_email (destinatario, assunto, corpo, transporte, status, erro) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$para, $assunto, $corpo, $transporte, $status, $erro]);
    } catch (PDOException $e) {
        if (DEBUG) {
            error_log('Falha ao registrar notificação: ' . $e->getMessage());
        }
    }

    return [
        'ok' => $status === 'enviado',
        'status' => $status,
        'erro' => $erro,
        'transporte' => $transporte,
    ];
}

/**
 * Contas sem acesso há pelo menos $meses e ainda sem aviso registrado.
 */
function contasParaAviso(int $meses = INATIVIDADE_MESES, ?PDO $db = null): array {
    $db = $db ?? getDB();
    $sql = "SELECT id, nome, email, COALESCE(ultimo_acesso_em, data_cadastro) AS ultimo_acesso
            FROM estudantes
            WHERE inatividade_aviso_em IS NULL
              AND COALESCE(ultimo_acesso_em, data_cadastro) <= datetime('now', '-" . max(1, $meses) . " months')
            ORDER BY ultimo_acesso ASC";
    return $db->query($sql)->fetchAll();
}

/**
 * Contas avisadas há $dias ou mais e que não voltaram a acessar.
 */
function contasParaExclusao(int $diasCarencia = INATIVIDADE_GRACA_DIAS, ?PDO $db = null): array {
    $db = $db ?? getDB();
    $sql = "SELECT id, nome, email, inatividade_aviso_em
            FROM estudantes
            WHERE inatividade_aviso_em IS NOT NULL
              AND inatividade_aviso_em <= datetime('now', '-" . max(1, $diasCarencia) . " days')
              AND COALESCE(ultimo_acesso_em, data_cadastro) <= inatividade_aviso_em
            ORDER BY inatividade_aviso_em ASC";
    return $db->query($sql)->fetchAll();
}

/**
 * Texto do aviso enviado ao titular.
 */
function textoAvisoInatividade(array $estudante, int $meses = INATIVIDADE_MESES, int $diasCarencia = INATIVIDADE_GRACA_DIAS): string {
    $nome = $estudante['nome'] ?? 'estudante';
    $ultimo = $estudante['ultimo_acesso'] ?? null;
    $desde = $ultimo ? date('d/m/Y', strtotime($ultimo)) : 'o cadastro';
    $limite = date('d/m/Y', strtotime("+{$diasCarencia} days"));

    return "Olá, {$nome}.\n\n"
        . "Sua conta no sistema de avaliação EN_430 está sem acesso desde {$desde}.\n"
        . "Pela nossa Política de Privacidade (item 8.2), contas inativas por mais de {$meses} meses\n"
        . "podem ser excluídas.\n\n"
        . "Se quiser manter sua conta e seu histórico de avaliações, basta entrar em:\n"
        . APP_URL . "/login\n\n"
        . "Se nenhum acesso ocorrer até {$limite}, sua conta e suas avaliações serão excluídas\n"
        . "automaticamente. Você pode se cadastrar novamente depois, mas o histórico anterior\n"
        . "não será recuperado.\n\n"
        . "Se preferir excluir agora, use a opção \"Excluir minha conta\" no painel ou escreva para\n"
        . "privacidade@en430.appservice.tec.br.\n\n"
        . "Política de Privacidade: " . APP_URL . "/politica-de-privacidade\n\n"
        . "— Projeto EN_430 (mensagem automática, não responda a este e-mail)";
}

/**
 * Executa a rotina: reativa contas que voltaram, avisa as inativas e exclui as
 * que passaram da carência com aviso entregue.
 *
 * @param bool $dryRun Apenas simula: não envia, não altera e não exclui.
 * @param callable|null $enviar Injetável para testes (assinatura de enviarEmail).
 * @return array<string, mixed>
 */
function processarRotinaInatividade(bool $dryRun = false, ?callable $enviar = null): array {
    $db = getDB();
    $enviar = $enviar ?? 'enviarEmail';

    $resumo = [
        'dry_run' => $dryRun,
        'meses' => INATIVIDADE_MESES,
        'graca_dias' => INATIVIDADE_GRACA_DIAS,
        'transporte' => MAIL_TRANSPORT,
        'reativadas' => 0,
        'avisos' => ['candidatos' => 0, 'enviados' => 0, 'falhas' => 0],
        'exclusoes' => ['candidatos' => 0, 'excluidos' => 0],
        'detalhes' => [],
    ];

    // 1. Quem voltou a acessar depois do aviso sai da rotina
    if (!$dryRun) {
        $reativadas = $db->exec("UPDATE estudantes SET inatividade_aviso_em = NULL
            WHERE inatividade_aviso_em IS NOT NULL
              AND ultimo_acesso_em IS NOT NULL
              AND ultimo_acesso_em > inatividade_aviso_em");
        $resumo['reativadas'] = (int)$reativadas;
    }

    // 2. Avisos para contas inativas sem aviso registrado
    $alvosAviso = contasParaAviso(INATIVIDADE_MESES, $db);
    $resumo['avisos']['candidatos'] = count($alvosAviso);

    foreach ($alvosAviso as $conta) {
        if ($dryRun) {
            $resumo['detalhes'][] = ['acao' => 'aviso_simulado', 'id' => (int)$conta['id']];
            continue;
        }

        $resultado = $enviar(
            $conta['email'],
            'Sua conta no EN_430 será excluída por inatividade',
            textoAvisoInatividade($conta)
        );

        if (!empty($resultado['ok'])) {
            $db->prepare("UPDATE estudantes SET inatividade_aviso_em = datetime('now') WHERE id = ?")
                ->execute([(int)$conta['id']]);
            $resumo['avisos']['enviados']++;
            $resumo['detalhes'][] = ['acao' => 'aviso_enviado', 'id' => (int)$conta['id']];
        } else {
            $resumo['avisos']['falhas']++;
            $resumo['detalhes'][] = [
                'acao' => 'aviso_falhou',
                'id' => (int)$conta['id'],
                'status' => $resultado['status'] ?? 'erro',
            ];
        }
    }

    // 3. Exclusão das contas cuja carência terminou
    $alvosExclusao = contasParaExclusao(INATIVIDADE_GRACA_DIAS, $db);
    $resumo['exclusoes']['candidatos'] = count($alvosExclusao);

    foreach ($alvosExclusao as $conta) {
        if ($dryRun) {
            $resumo['detalhes'][] = ['acao' => 'exclusao_simulada', 'id' => (int)$conta['id']];
            continue;
        }

        // Só chega aqui quem tem inatividade_aviso_em registrado, e esse campo
        // só é gravado quando o aviso foi realmente entregue.
        try {
            $db->beginTransaction();
            $db->prepare("DELETE FROM avaliacoes WHERE estudante_id = ?")->execute([(int)$conta['id']]);
            $db->prepare("DELETE FROM estudantes WHERE id = ?")->execute([(int)$conta['id']]);
            $db->commit();

            adminLog('exclusao_conta_inativa', [
                'id' => (int)$conta['id'],
                'email' => $conta['email'],
                'inatividade_meses' => INATIVIDADE_MESES,
                'origem' => 'rotina_inatividade',
            ]);
            $resumo['exclusoes']['excluidos']++;
            $resumo['detalhes'][] = ['acao' => 'exclusao', 'id' => (int)$conta['id']];
        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            if (DEBUG) {
                error_log('Falha ao excluir conta inativa: ' . $e->getMessage());
            }
            $resumo['detalhes'][] = ['acao' => 'exclusao_erro', 'id' => (int)$conta['id']];
        }
    }

    if (!$dryRun) {
        adminLog('rotina_inatividade', [
            'meses' => INATIVIDADE_MESES,
            'graca_dias' => INATIVIDADE_GRACA_DIAS,
            'transporte' => MAIL_TRANSPORT,
            'avisos_enviados' => $resumo['avisos']['enviados'],
            'avisos_falhas' => $resumo['avisos']['falhas'],
            'excluidos' => $resumo['exclusoes']['excluidos'],
            'reativadas' => $resumo['reativadas'],
        ]);
    }

    return $resumo;
}

/**
 * Números para o painel administrativo.
 */
function resumoInatividade(?PDO $db = null): array {
    $db = $db ?? getDB();
    $resumo = [
        'a_avisar' => 0,
        'em_carencia' => 0,
        'elegiveis' => 0,
        'reativadas' => 0,
        'ultima_execucao' => null,
        'ultimos_avisos' => [],
    ];

    try {
        $resumo['a_avisar'] = count(contasParaAviso(INATIVIDADE_MESES, $db));
        $resumo['elegiveis'] = count(contasParaExclusao(INATIVIDADE_GRACA_DIAS, $db));

        $stmt = $db->query("SELECT COUNT(*) FROM estudantes
            WHERE inatividade_aviso_em IS NOT NULL
              AND inatividade_aviso_em > datetime('now', '-" . INATIVIDADE_GRACA_DIAS . " days')");
        $resumo['em_carencia'] = (int)$stmt->fetchColumn();

        $db->exec("CREATE TABLE IF NOT EXISTS admin_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            acao TEXT NOT NULL,
            detalhes TEXT,
            data TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $ultima = $db->query("SELECT data FROM admin_log WHERE acao = 'rotina_inatividade' ORDER BY id DESC LIMIT 1")->fetchColumn();
        $resumo['ultima_execucao'] = $ultima ?: null;

        ensureNotificacoesTable($db);
        $resumo['ultimos_avisos'] = $db->query("SELECT destinatario, status, criado_em
            FROM notificacoes_email ORDER BY id DESC LIMIT 5")->fetchAll();
    } catch (PDOException $e) {
        if (DEBUG) {
            error_log('Falha ao resumir inatividade: ' . $e->getMessage());
        }
    }

    return $resumo;
}
