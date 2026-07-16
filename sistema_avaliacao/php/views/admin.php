<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Administração — Sistema de Avaliação</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<div class="admin-topbar">
  <h1>⚙️ Administração do Sistema <span class="badge">🔒 Acesso Exclusivo</span> <span class="badge">EN_430</span></h1>
  <div class="admin-actions">
    <a href="<?= url('') ?>">🏠 Sistema</a>
    <a href="<?= url('admin') ?>?logout_admin=1">🚪 Sair</a>
  </div>
</div>

<div class="container">
  <?= $flashes ?? '' ?>

  <!-- ===== ESTATÍSTICAS GERAIS ===== -->
  <div class="stats-row">
    <div class="stat-card"><div class="stat-num" style="color:#0a3d62;"><?= (int)($stats['total_estudantes'] ?? 0) ?></div><div class="stat-label">👥 Estudantes</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#2e7d32;"><?= (int)($stats['total_questoes'] ?? 0) ?></div><div class="stat-label">📝 Questões</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#e65100;"><?= (int)($stats['total_avaliacoes'] ?? 0) ?></div><div class="stat-label">📊 Avaliações</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#0a3d62;"><?= (int)($stats['questoes_facil'] ?? 0) ?></div><div class="stat-label">🟢 Fáceis</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#e65100;"><?= (int)($stats['questoes_medio'] ?? 0) ?></div><div class="stat-label">🟡 Médias</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#6a1b9a;"><?= (int)($stats['questoes_dificil'] ?? 0) ?></div><div class="stat-label">🔴 Difíceis</div></div>
  </div>

  <!-- ===== CONFIGURAÇÃO ===== -->
  <div class="admin-card">
    <div class="admin-card-header" onclick="toggleCard(this)">
      <span>⚙️</span><h2>Configuração e Detalhes do Projeto</h2><span class="toggle open">▶</span>
    </div>
    <div class="admin-card-body">
      <div class="info-grid">
        <div class="info-item"><div class="info-label">Disciplina</div><div class="info-value">Introdução à Enfermagem (EN_430)</div></div>
        <div class="info-item"><div class="info-label">Curso</div><div class="info-value">Enfermagem — EAD / Subsequente</div></div>
        <div class="info-item"><div class="info-label">Framework</div><div class="info-value">PHP 8.4+ (PDO SQLite)</div></div>
        <div class="info-item"><div class="info-label">Banco de Dados</div><div class="info-value">SQLite 3 (avaliacao.db)</div></div>
        <div class="info-item"><div class="info-label">Hash de Senhas</div><div class="info-value">bcrypt (password_hash)</div></div>
        <div class="info-item"><div class="info-label">CSRF</div><div class="info-value">Tokens de sessão (SHA-256)</div></div>
        <div class="info-item"><div class="info-label">Módulos</div><div class="info-value">10 módulos de conteúdo</div></div>
        <div class="info-item"><div class="info-label">Versão</div><div class="info-value">2.0 (PHP + JS)</div></div>
      </div>
      <div class="section-title">📁 Estrutura do Projeto</div>
      <div class="guide-section">
        <pre style="background:#1a1a2e;color:#e8ecf0;border-radius:6px;padding:12px;overflow-x:auto;font-size:0.8em;margin:0;">
php/
├── index.php                    # Front controller (roteador)
├── .htaccess                    # URL rewriting
├── config.php                   # Configurações
├── functions.php                # Funções auxiliares
├── db.php                       # Conexão PDO SQLite
├── assets/
│   ├── css/style.css            # Estilos consolidados
│   └── js/app.js                # JavaScript (máscaras + interações)
├── views/
│   ├── index.php                # Página inicial
│   ├── cadastro.php             # Cadastro de estudante
│   ├── login.php                # Login
│   ├── recuperar_acesso.php     # Recuperação de senha
│   ├── painel.php               # Dashboard do estudante
│   ├── avaliacao.php            # Avaliação (questões)
│   ├── resultado.php            # Resultado da avaliação
│   ├── progresso.php            # Progresso por módulo
│   ├── admin_login.php          # Login administrativo
│   └── admin.php                # ⚙️ Painel administrativo
├── scripts/
│   ├── migrate.php              # Schema + seed (1500+ questões)
│   └── recriar_questoes.php     # Recria com 3000 questões
└── avaliacao.db                 # Banco SQLite
        </pre>
      </div>
      <div class="section-title">🔀 Rotas da Aplicação</div>
      <table>
        <tr><th>Rota</th><th>Métodos</th><th>Descrição</th></tr>
        <tr><td><code>/</code></td><td>GET</td><td>Página inicial</td></tr>
        <tr><td><code>/cadastro</code></td><td>GET, POST</td><td>Cadastro de estudante</td></tr>
        <tr><td><code>/login</code></td><td>GET, POST</td><td>Login</td></tr>
        <tr><td><code>/logout</code></td><td>GET</td><td>Logout</td></tr>
        <tr><td><code>/recuperar-acesso</code></td><td>GET, POST</td><td>Recuperação (Nome + Telefone)</td></tr>
        <tr><td><code>/redefinir-senha</code></td><td>POST</td><td>Redefinição de senha</td></tr>
        <tr><td><code>/painel</code></td><td>GET</td><td>Dashboard do estudante</td></tr>
        <tr><td><code>/nova-avaliacao</code></td><td>POST</td><td>Gerar avaliação (com filtro)</td></tr>
        <tr><td><code>/avaliacao/{id}</code></td><td>GET</td><td>Responder avaliação</td></tr>
        <tr><td><code>/avaliacao/{id}/responder</code></td><td>POST</td><td>Submeter respostas</td></tr>
        <tr><td><code>/avaliacao/{id}/resultado</code></td><td>GET</td><td>Resultado da avaliação</td></tr>
        <tr><td><code>/progresso</code></td><td>GET</td><td>Progresso (?dificuldade=)</td></tr>
        <tr><td><code>/admin</code></td><td>GET, POST</td><td>⚙️ Administração</td></tr>
        <tr><td><code>/admin-login</code></td><td>GET, POST</td><td>Login administrativo</td></tr>
      </table>
    </div>
  </div>

  <!-- ===== ESTUDANTES ===== -->
  <div class="admin-card">
    <div class="admin-card-header" onclick="toggleCard(this)">
      <span>👥</span><h2>Controle de Estudantes (<?= (int)($stats['total_estudantes'] ?? 0) ?>)</h2><span class="toggle">▶</span>
    </div>
    <div class="admin-card-body hidden">
      <div class="student-list">
        <table>
          <tr><th>#</th><th>Nome</th><th>Email</th><th>📅 Nascimento</th><th>📞 Telefone</th><th>📊 Avaliações</th><th>📅 Cadastro</th></tr>
          <?php foreach ($estudantes as $e): ?>
          <tr>
            <td><?= (int)$e['id'] ?></td>
            <td><strong><?= htmlspecialchars($e['nome']) ?></strong></td>
            <td><?= htmlspecialchars($e['email']) ?></td>
            <td><?= htmlspecialchars($e['data_nascimento'] ?? '—') ?></td>
            <td><?= htmlspecialchars($e['telefone'] ?? '—') ?></td>
            <td><span class="tag <?= ($stats_avaliacoes_por_estudante[$e['id']] ?? 0) > 0 ? 'pass' : 'warn' ?>"><?= (int)($stats_avaliacoes_por_estudante[$e['id']] ?? 0) ?></span></td>
            <td><?= !empty($e['data_cadastro']) ? substr($e['data_cadastro'], 0, 10) : '—' ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>

  <!-- ===== QUESTÕES ===== -->
  <div class="admin-card">
    <div class="admin-card-header" onclick="toggleCard(this)">
      <span>📝</span><h2>Controle de Questões (<?= (int)($stats['total_questoes'] ?? 0) ?>)</h2><span class="toggle">▶</span>
    </div>
    <div class="admin-card-body hidden">
      <div class="section-title">Distribuição por Dificuldade</div>
      <?php $totalQ = (int)($stats['total_questoes'] ?? 1); ?>
      <table>
        <tr><th>Nível</th><th>Quantidade</th><th>%</th><th>Visual</th></tr>
        <tr><td>🟢 Fácil</td><td><?= (int)($stats['questoes_facil'] ?? 0) ?></td><td><?= $totalQ > 0 ? round(($stats['questoes_facil'] ?? 0) * 100 / $totalQ, 1) : 0 ?>%</td>
          <td><div class="module-bar"><div class="bar facil" style="width:<?= $totalQ > 0 ? ($stats['questoes_facil'] ?? 0) * 100 / $totalQ : 0 ?>%"></div></div></td></tr>
        <tr><td>🟡 Médio</td><td><?= (int)($stats['questoes_medio'] ?? 0) ?></td><td><?= $totalQ > 0 ? round(($stats['questoes_medio'] ?? 0) * 100 / $totalQ, 1) : 0 ?>%</td>
          <td><div class="module-bar"><div class="bar medio" style="width:<?= $totalQ > 0 ? ($stats['questoes_medio'] ?? 0) * 100 / $totalQ : 0 ?>%"></div></div></td></tr>
        <tr><td>🔴 Difícil</td><td><?= (int)($stats['questoes_dificil'] ?? 0) ?></td><td><?= $totalQ > 0 ? round(($stats['questoes_dificil'] ?? 0) * 100 / $totalQ, 1) : 0 ?>%</td>
          <td><div class="module-bar"><div class="bar dificil" style="width:<?= $totalQ > 0 ? ($stats['questoes_dificil'] ?? 0) * 100 / $totalQ : 0 ?>%"></div></div></td></tr>
      </table>

      <div class="section-title">Distribuição por Módulo</div>
      <table>
        <tr><th>Módulo</th><th>Total</th><th>🟢 Fácil</th><th>🟡 Médio</th><th>🔴 Difícil</th></tr>
        <?php foreach ($questoes_por_modulo as $m): ?>
        <tr>
          <td><strong>Módulo <?= (int)$m['modulo'] ?></strong></td>
          <td><?= (int)$m['total'] ?></td><td><?= (int)$m['facil'] ?></td><td><?= (int)$m['medio'] ?></td><td><?= (int)$m['dificil'] ?></td>
        </tr>
        <?php endforeach; ?>
      </table>

      <div class="section-title">Últimas Questões Adicionadas</div>
      <table>
        <tr><th>ID</th><th>Módulo</th><th>Dificuldade</th><th>Texto (início)</th><th>Resposta</th></tr>
        <?php foreach ($ultimas_questoes as $q): ?>
        <tr>
          <td><?= (int)$q['id'] ?></td>
          <td><span class="tag info">M<?= (int)$q['modulo'] ?></span></td>
          <td><?php if ($q['dificuldade'] == 'Fácil'): ?><span class="tag pass">🟢 Fácil</span>
              <?php elseif ($q['dificuldade'] == 'Médio'): ?><span class="tag warn">🟡 Médio</span>
              <?php else: ?><span class="tag" style="background:#fce4e4;color:#c62828;">🔴 Difícil</span><?php endif; ?></td>
          <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars(mb_substr($q['texto'], 0, 80)) ?><?= mb_strlen($q['texto']) > 80 ? '...' : '' ?></td>
          <td><strong><?= htmlspecialchars($q['resposta']) ?></strong></td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</div>

<script>
function toggleCard(header) {
  const body = header.nextElementSibling;
  if (body) {
    body.classList.toggle('hidden');
    const toggle = header.querySelector('.toggle');
    if (toggle) toggle.classList.toggle('open');
  }
}
</script>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
