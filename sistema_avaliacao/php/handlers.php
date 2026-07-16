<?php
/**
 * handlers.php — Handlers (Controladores) do Sistema de Avaliação
 * 
 * Separado do index.php para permitir include múltiplo em testes
 * sem erro de redeclaração de funções. Usar SEMPRE com require_once.
 *
 * Este arquivo contém todas as funções handler que processam as rotas.
 * Depende de config.php e functions.php (já carregados pelo index.php).
 */

// ═══════════════════════════════════════════════════════════════
// HANDLERS
// ═══════════════════════════════════════════════════════════════

/**
 * Cadastro de novo estudante
 */
function handleCadastro(): void {
    $nome = trim($_POST['nome'] ?? '');
    $dataNascimento = trim($_POST['data_nascimento'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';
    
    // Validações
    $erros = [];
    if (!$nome) $erros[] = 'Nome é obrigatório.';
    if (!validarEmail($email)) $erros[] = 'Email inválido.';
    if (!validarSenha($senha)) $erros[] = 'Senha deve ter no mínimo 4 caracteres.';
    if ($senha !== $confirmar) $erros[] = 'Senhas não conferem.';
    
    if ($erros) {
        foreach ($erros as $e) flash('erro', $e);
        view('cadastro', ['dados' => $_POST]);
        return;
    }
    
    $db = getDB();
    try {
        $stmt = $db->prepare("INSERT INTO estudantes (nome, data_nascimento, telefone, email, senha_hash) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $dataNascimento ?: null, $telefone ?: null, $email, hashSenha($senha)]);
        
        flash('sucesso', '✅ Cadastro realizado com sucesso! Faça login.');
        redirect('login');
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            flash('erro', '❌ Este email já está cadastrado.');
        } else {
            flash('erro', '❌ Erro ao cadastrar. Tente novamente.');
            if (DEBUG) error_log("Erro cadastro: " . $e->getMessage());
        }
        view('cadastro', ['dados' => $_POST]);
    }
}

/**
 * Login do estudante
 */
function handleLogin(): void {
    // Verificar rate limiting
    if (checkLoginAttempts()) {
        $tempo = getLoginBlockTime();
        $minutos = ceil($tempo / 60);
        flash('erro', "🔒 Muitas tentativas de login. Aguarde {$minutos} minuto(s) antes de tentar novamente.");
        view('login');
        return;
    }
    
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM estudantes WHERE email = ?");
    $stmt->execute([$email]);
    $estudante = $stmt->fetch();
    
    if ($estudante && verificarSenha($senha, $estudante['senha_hash'])) {
        recordLoginAttempt(true); // Resetar tentativas
        $_SESSION['estudante_id'] = (int)$estudante['id'];
        $_SESSION['estudante_nome'] = $estudante['nome'];
        regenerateSessionId();
        flash('sucesso', "👋 Bem-vindo(a), {$estudante['nome']}!");
        redirect('painel');
    } else {
        recordLoginAttempt(false); // Registrar falha
        flash('erro', '❌ Email ou senha incorretos.');
        view('login');
    }
}

/**
 * Recuperar acesso (Nome + Telefone)
 */
function handleRecuperarAcesso(): void {
    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    
    if (!$nome || !$telefone) {
        flash('erro', '❌ Informe seu nome e telefone cadastrados.');
        view('recuperar_acesso');
        return;
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM estudantes WHERE nome = ? AND telefone = ?");
    $stmt->execute([$nome, $telefone]);
    $estudante = $stmt->fetch();
    
    if ($estudante) {
        flash('sucesso', "✅ Conta encontrada! Seu email de acesso é: {$estudante['email']}");
        flash('info', '📝 Agora você pode redefinir sua senha abaixo.');
        view('recuperar_acesso', [
            'encontrado' => true,
            'nome' => $nome,
            'telefone' => $telefone,
            'email' => $estudante['email'],
            'estudante_id' => $estudante['id'],
        ]);
    } else {
        flash('erro', '❌ Nome e telefone não correspondem a nenhum cadastro.');
        view('recuperar_acesso');
    }
}

/**
 * Redefinir senha
 */
function handleRedefinirSenha(): void {
    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';
    
    if (!$nome || !$telefone) {
        flash('erro', '❌ Informe seu nome e telefone.');
        redirect('recuperar-acesso');
    }
    if (!validarSenha($novaSenha)) {
        flash('erro', '❌ A senha deve ter no mínimo 4 caracteres.');
        view('recuperar_acesso', ['encontrado' => true, 'nome' => $nome, 'telefone' => $telefone]);
        return;
    }
    if ($novaSenha !== $confirmar) {
        flash('erro', '❌ As senhas não conferem.');
        view('recuperar_acesso', ['encontrado' => true, 'nome' => $nome, 'telefone' => $telefone]);
        return;
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM estudantes WHERE nome = ? AND telefone = ?");
    $stmt->execute([$nome, $telefone]);
    $estudante = $stmt->fetch();
    
    if ($estudante) {
        $stmt = $db->prepare("UPDATE estudantes SET senha_hash = ? WHERE id = ?");
        $stmt->execute([hashSenha($novaSenha), $estudante['id']]);
        flash('sucesso', "✅ Senha redefinida com sucesso! Faça login com seu email ({$estudante['email']}) e a nova senha.");
        redirect('login');
    } else {
        flash('erro', '❌ Dados não conferem. Tente novamente.');
        view('recuperar_acesso');
    }
}

/**
 * Painel do estudante
 */
function handlePainel(): void {
    $estudanteId = getEstudanteId();
    $db = getDB();
    
    // Dados do estudante
    $stmt = $db->prepare("SELECT * FROM estudantes WHERE id = ?");
    $stmt->execute([$estudanteId]);
    $estudante = $stmt->fetch();
    
    // Histórico de avaliações
    $stmt = $db->prepare("SELECT * FROM avaliacoes WHERE estudante_id = ? ORDER BY data_inicio DESC LIMIT 20");
    $stmt->execute([$estudanteId]);
    $avaliacoes = $stmt->fetchAll();
    
    // Estatísticas
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_avaliacoes,
            COALESCE(AVG(pontuacao * 100.0 / total_questoes), 0) as media_acertos,
            COALESCE(MAX(pontuacao * 100.0 / total_questoes), 0) as melhor_nota,
            COUNT(CASE WHEN status = 'em_andamento' THEN 1 END) as em_andamento
        FROM avaliacoes 
        WHERE estudante_id = ?
    ");
    $stmt->execute([$estudanteId]);
    $stats = $stmt->fetch();
    
    view('painel', [
        'estudante' => $estudante,
        'avaliacoes' => $avaliacoes,
        'stats' => $stats,
    ]);
}

/**
 * Gerar nova avaliação
 */
function handleNovaAvaliacao(): void {
    $estudanteId = getEstudanteId();
    $dificuldade = trim($_POST['dificuldade'] ?? '');
    $db = getDB();
    
    // Buscar IDs de questões já respondidas
    $respondidas = [];
    $stmt = $db->prepare("SELECT questoes_ids FROM avaliacoes WHERE estudante_id = ? AND status = 'concluido'");
    $stmt->execute([$estudanteId]);
    $anteriores = $stmt->fetchAll();
    
    foreach ($anteriores as $av) {
        $ids = json_decode($av['questoes_ids'], true) ?? [];
        $respondidas = array_merge($respondidas, $ids);
    }
    $respondidasSet = array_unique($respondidas);
    
    // Montar query com filtro
    if (in_array($dificuldade, ['Fácil', 'Médio', 'Difícil'])) {
        $sql = "SELECT id FROM questoes WHERE dificuldade = ?";
        $params = [$dificuldade];
        $filtroNome = ['Fácil' => '🟢 Fáceis', 'Médio' => '🟡 Médias', 'Difícil' => '🔴 Difíceis'][$dificuldade];
    } else {
        $sql = "SELECT id FROM questoes";
        $params = [];
        $dificuldade = 'todas';
        $filtroNome = 'Todas';
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $disponiveis = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Filtrar não respondidas
    $naoRespondidas = array_values(array_diff($disponiveis, $respondidasSet));
    
    if (empty($naoRespondidas)) {
        // Se todas já foram respondidas, permitir repetir
        $naoRespondidas = $disponiveis;
    }
    
    if (empty($naoRespondidas)) {
        flash('erro', "❌ Não há questões {$filtroNome} disponíveis. Tente outro nível.");
        redirect('painel');
    }
    
    // Selecionar questões aleatórias
    $quantidade = min(QUESTOES_POR_AVALIACAO, count($naoRespondidas));
    $selecionadas = array_rand(array_flip($naoRespondidas), $quantidade);
    if (!is_array($selecionadas)) $selecionadas = [$selecionadas];
    
    $questoesIdsJson = json_encode(array_values($selecionadas));
    
    // Criar avaliação
    $stmt = $db->prepare("INSERT INTO avaliacoes (estudante_id, total_questoes, questoes_ids, status) VALUES (?, ?, ?, 'em_andamento')");
    $stmt->execute([$estudanteId, $quantidade, $questoesIdsJson]);
    $avaliacaoId = $db->lastInsertId();
    
    flash('info', "📝 Avaliação gerada com questões {$filtroNome}!");
    redirect("avaliacao/{$avaliacaoId}");
}

/**
 * Responder avaliação (GET)
 */
function handleResponderAvaliacao(int $avaliacaoId): void {
    $estudanteId = getEstudanteId();
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM avaliacoes WHERE id = ? AND estudante_id = ?");
    $stmt->execute([$avaliacaoId, $estudanteId]);
    $avaliacao = $stmt->fetch();
    
    if (!$avaliacao) {
        flash('erro', '❌ Avaliação não encontrada.');
        redirect('painel');
        return; // Segurança: redirect pode retornar em modo de teste
    }
    
    if ($avaliacao['status'] === 'concluido') {
        redirect("avaliacao/{$avaliacaoId}/resultado");
        return; // Segurança: redirect pode retornar em modo de teste
    }
    
    // Buscar questões
    $questoesIds = json_decode($avaliacao['questoes_ids'], true) ?? [];
    if (empty($questoesIds)) {
        flash('erro', '❌ Erro ao carregar questões.');
        redirect('painel');
    }
    
    $placeholders = implode(',', array_fill(0, count($questoesIds), '?'));
    $stmt = $db->prepare("SELECT * FROM questoes WHERE id IN ({$placeholders})");
    $stmt->execute($questoesIds);
    $questoesMap = [];
    foreach ($stmt->fetchAll() as $q) {
        $questoesMap[$q['id']] = $q;
    }
    
    // Manter ordem original
    $questoes = [];
    foreach ($questoesIds as $qid) {
        if (isset($questoesMap[$qid])) {
            $questoes[] = $questoesMap[$qid];
        }
    }
    
    view('avaliacao', [
        'avaliacao' => $avaliacao,
        'questoes' => $questoes,
    ]);
}

/**
 * Submeter respostas (POST)
 */
function handleSubmeterRespostas(int $avaliacaoId): void {
    $estudanteId = getEstudanteId();
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM avaliacoes WHERE id = ? AND estudante_id = ? AND status = 'em_andamento'");
    $stmt->execute([$avaliacaoId, $estudanteId]);
    $avaliacao = $stmt->fetch();
    
    if (!$avaliacao) {
        flash('erro', '❌ Avaliação não encontrada ou já concluída.');
        redirect('painel');
        return; // Segurança: redirect pode retornar em modo de teste
    }
    
    $questoesIds = json_decode($avaliacao['questoes_ids'], true) ?? [];
    $respostas = [];
    $resultado = [];
    $pontuacao = 0;
    
    foreach ($questoesIds as $qid) {
        $respostaEstudante = strtoupper(trim($_POST["q_{$qid}"] ?? ''));
        $respostas[(string)$qid] = $respostaEstudante;
        
        $stmt = $db->prepare("SELECT * FROM questoes WHERE id = ?");
        $stmt->execute([$qid]);
        $questao = $stmt->fetch();
        
        if ($questao) {
            $correta = ($respostaEstudante === $questao['resposta']);
            if ($correta) $pontuacao++;
            
            $resultado[(string)$qid] = [
                'correta' => $correta,
                'resposta_estudante' => $respostaEstudante,
                'resposta_correta' => $questao['resposta'],
                'texto' => $questao['texto'],
                'dificuldade' => $questao['dificuldade'] ?? 'Médio',
                'modulo' => $questao['modulo'],
                'opcoes' => [
                    'A' => $questao['opcao_a'],
                    'B' => $questao['opcao_b'],
                    'C' => $questao['opcao_c'],
                    'D' => $questao['opcao_d'],
                ],
            ];
        }
    }
    
    // Atualizar avaliação
    $stmt = $db->prepare("
        UPDATE avaliacoes 
        SET respostas = ?, resultado = ?, pontuacao = ?, status = 'concluido', data_fim = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    $stmt->execute([
        json_encode($respostas),
        json_encode($resultado),
        $pontuacao,
        $avaliacaoId,
    ]);
    
    flash('sucesso', "✅ Avaliação concluída! Você acertou {$pontuacao} de {$avaliacao['total_questoes']} questões.");
    redirect("avaliacao/{$avaliacaoId}/resultado");
}

/**
 * Resultado da avaliação
 */
function handleResultado(int $avaliacaoId): void {
    $estudanteId = getEstudanteId();
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM avaliacoes WHERE id = ? AND estudante_id = ? AND status = 'concluido'");
    $stmt->execute([$avaliacaoId, $estudanteId]);
    $avaliacao = $stmt->fetch();
    
    if (!$avaliacao) {
        flash('erro', '❌ Resultado não encontrado.');
        redirect('painel');
        return; // Segurança: redirect pode retornar em modo de teste
    }
    
    $resultado = json_decode($avaliacao['resultado'], true) ?? [];
    $questoesIds = json_decode($avaliacao['questoes_ids'], true) ?? [];
    
    view('resultado', [
        'avaliacao' => $avaliacao,
        'resultado' => $resultado,
        'questoes_ids' => $questoesIds,
    ]);
}

/**
 * Página de progresso
 */
function handleProgresso(): void {
    $estudanteId = getEstudanteId();
    $dificuldade = trim($_GET['dificuldade'] ?? 'todas');
    $db = getDB();
    
    // Preparar filtro SQL
    $temFiltro = in_array($dificuldade, ['Fácil', 'Médio', 'Difícil']);
    $filtroNome = $temFiltro 
        ? ['Fácil' => '🟢 Fáceis', 'Médio' => '🟡 Médias', 'Difícil' => '🔴 Difíceis'][$dificuldade]
        : '📚 Todas';
    
    // Progresso por módulo
    if ($temFiltro) {
        $progressoModulos = $db->prepare("
            SELECT 
                q.modulo,
                COUNT(DISTINCT q.id) as total_questoes,
                COUNT(DISTINCT responded.qid) as respondidas
            FROM questoes q
            LEFT JOIN (
                SELECT DISTINCT json_each.value as qid
                FROM avaliacoes, json_each(avaliacoes.questoes_ids)
                WHERE avaliacoes.estudante_id = ? AND avaliacoes.status = 'concluido'
            ) responded ON q.id = responded.qid
            WHERE q.dificuldade = ?
            GROUP BY q.modulo
            ORDER BY q.modulo
        ");
        $progressoModulos->execute([$estudanteId, $dificuldade]);
        
        $acertosModulos = $db->prepare("
            SELECT 
                q.modulo,
                COUNT(*) as total_respondidas,
                SUM(CASE WHEN j.value = q.resposta THEN 1 ELSE 0 END) as acertos
            FROM questoes q
            JOIN avaliacoes a ON a.estudante_id = ? AND a.status = 'concluido'
            JOIN json_each(a.questoes_ids) je ON q.id = je.value
            JOIN json_each(a.respostas) j ON j.key = je.value
            WHERE q.dificuldade = ?
            GROUP BY q.modulo
            ORDER BY q.modulo
        ");
        $acertosModulos->execute([$estudanteId, $dificuldade]);
    } else {
        $progressoModulos = $db->prepare("
            SELECT 
                q.modulo,
                COUNT(DISTINCT q.id) as total_questoes,
                COUNT(DISTINCT responded.qid) as respondidas
            FROM questoes q
            LEFT JOIN (
                SELECT DISTINCT json_each.value as qid
                FROM avaliacoes, json_each(avaliacoes.questoes_ids)
                WHERE avaliacoes.estudante_id = ? AND avaliacoes.status = 'concluido'
            ) responded ON q.id = responded.qid
            GROUP BY q.modulo
            ORDER BY q.modulo
        ");
        $progressoModulos->execute([$estudanteId]);
        
        $acertosModulos = $db->prepare("
            SELECT 
                q.modulo,
                COUNT(*) as total_respondidas,
                SUM(CASE WHEN j.value = q.resposta THEN 1 ELSE 0 END) as acertos
            FROM questoes q
            JOIN avaliacoes a ON a.estudante_id = ? AND a.status = 'concluido'
            JOIN json_each(a.questoes_ids) je ON q.id = je.value
            JOIN json_each(a.respostas) j ON j.key = je.value
            GROUP BY q.modulo
            ORDER BY q.modulo
        ");
        $acertosModulos->execute([$estudanteId]);
    }
    
    // Estatísticas por dificuldade
    $statsDificuldade = $db->prepare("
        SELECT 
            q.dificuldade,
            COUNT(DISTINCT q.id) as total_banco,
            COUNT(DISTINCT je_d.qid) as respondidas,
            COALESCE(SUM(CASE WHEN j.resposta = q.resposta THEN 1 ELSE 0 END), 0) as acertos,
            COUNT(j.qid) as total_respondidas_real
        FROM questoes q
        LEFT JOIN (
            SELECT DISTINCT je.value as qid
            FROM avaliacoes, json_each(avaliacoes.questoes_ids) je
            WHERE avaliacoes.estudante_id = ? AND avaliacoes.status = 'concluido'
        ) je_d ON q.id = je_d.qid
        LEFT JOIN (
            SELECT je.value as qid, j.value as resposta
            FROM avaliacoes a
            CROSS JOIN json_each(a.questoes_ids) je
            JOIN json_each(a.respostas) j ON j.key = je.value
            WHERE a.estudante_id = ? AND a.status = 'concluido'
        ) j ON q.id = j.qid
        GROUP BY q.dificuldade
        ORDER BY 
            CASE q.dificuldade 
                WHEN 'Fácil' THEN 1 
                WHEN 'Médio' THEN 2 
                WHEN 'Difícil' THEN 3 
            END
    ");
    $statsDificuldade->execute([$estudanteId, $estudanteId]);
    
    view('progresso', [
        'progresso_modulos' => $progressoModulos->fetchAll(),
        'acertos_modulos' => $acertosModulos->fetchAll(),
        'stats_dificuldade' => $statsDificuldade->fetchAll(),
        'filtro_atual' => $dificuldade,
        'filtro_nome' => $filtroNome,
    ]);
}

/**
 * Admin - painel administrativo
 */
function handleAdmin(): void {
    // Verificar se tem sessão admin ativa
    if (!empty($_SESSION['admin_authenticated'])) {
        renderAdminPainel();
        return;
    }
    
    // Processar login admin via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $senha = trim($_POST['senha_admin'] ?? '');
        if ($senha === ADMIN_SECRET) {
            $_SESSION['admin_authenticated'] = true;
            regenerateSessionId();
            renderAdminPainel();
        } else {
            flash('erro', '❌ Senha de administração incorreta.');
            view('admin_login');
        }
        return;
    }
    
    view('admin_login');
}

/**
 * Login admin via POST separado
 */
function handleAdminLogin(): void {
    // Verificar rate limiting
    if (checkLoginAttempts()) {
        $tempo = getLoginBlockTime();
        $minutos = ceil($tempo / 60);
        flash('erro', "🔒 Muitas tentativas de login administrativo. Aguarde {$minutos} minuto(s) antes de tentar novamente.");
        view('admin_login');
        return;
    }
    
    $senha = trim($_POST['senha_admin'] ?? '');
    if ($senha === ADMIN_SECRET) {
        recordLoginAttempt(true); // Resetar tentativas
        $_SESSION['admin_authenticated'] = true;
        regenerateSessionId();
        redirect('admin');
    } else {
        recordLoginAttempt(false); // Registrar falha
        flash('erro', '❌ Senha de administração incorreta.');
        view('admin_login');
    }
}

/**
 * Renderizar o painel admin
 */
function renderAdminPainel(): void {
    $db = getDB();
    
    $totalEstudantes = $db->query("SELECT COUNT(*) FROM estudantes")->fetchColumn();
    $totalQuestoes = $db->query("SELECT COUNT(*) FROM questoes")->fetchColumn();
    $totalAvaliacoes = $db->query("SELECT COUNT(*) FROM avaliacoes")->fetchColumn();
    
    $questoesFacil = $db->query("SELECT COUNT(*) FROM questoes WHERE dificuldade = 'Fácil'")->fetchColumn();
    $questoesMedio = $db->query("SELECT COUNT(*) FROM questoes WHERE dificuldade = 'Médio'")->fetchColumn();
    $questoesDificil = $db->query("SELECT COUNT(*) FROM questoes WHERE dificuldade = 'Difícil'")->fetchColumn();
    
    $estudantes = $db->query("SELECT id, nome, email, data_nascimento, telefone, data_cadastro FROM estudantes ORDER BY id DESC")->fetchAll();
    
    $statsAv = $db->query("SELECT estudante_id, COUNT(*) as total FROM avaliacoes GROUP BY estudante_id")->fetchAll();
    $statsAvaliacoesPorEstudante = [];
    foreach ($statsAv as $row) {
        $statsAvaliacoesPorEstudante[$row['estudante_id']] = $row['total'];
    }
    
    $questoesPorModulo = $db->query("
        SELECT 
            modulo,
            COUNT(*) as total,
            SUM(CASE WHEN dificuldade = 'Fácil' THEN 1 ELSE 0 END) as facil,
            SUM(CASE WHEN dificuldade = 'Médio' THEN 1 ELSE 0 END) as medio,
            SUM(CASE WHEN dificuldade = 'Difícil' THEN 1 ELSE 0 END) as dificil
        FROM questoes
        GROUP BY modulo
        ORDER BY modulo
    ")->fetchAll();
    
    $ultimasQuestoes = $db->query("SELECT id, modulo, dificuldade, texto, resposta FROM questoes ORDER BY id DESC LIMIT 20")->fetchAll();
    
    view('admin', [
        'stats' => [
            'total_estudantes' => $totalEstudantes,
            'total_questoes' => $totalQuestoes,
            'total_avaliacoes' => $totalAvaliacoes,
            'questoes_facil' => $questoesFacil,
            'questoes_medio' => $questoesMedio,
            'questoes_dificil' => $questoesDificil,
        ],
        'estudantes' => $estudantes,
        'stats_avaliacoes_por_estudante' => $statsAvaliacoesPorEstudante,
        'questoes_por_modulo' => $questoesPorModulo,
        'ultimas_questoes' => $ultimasQuestoes,
    ]);
}

/**
 * Regenera ID da sessão (prevenção session fixation)
 */
function regenerateSessionId(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}
