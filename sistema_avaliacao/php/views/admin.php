<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Administração — Sistema de Avaliação EN_430</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<div class="admin-topbar">
  <h1>⚙️ Gestão do Sistema EN_430 <span class="badge">🔒 Acesso Exclusivo</span></h1>
  <div class="admin-actions">
    <a href="<?= url('') ?>">🏠 Ir para o Sistema</a>
    <a href="<?= url('admin') ?>?logout_admin=1" data-confirm="Tem certeza que deseja sair do painel administrativo?">🚪 Sair</a>
  </div>
</div>

<div class="container">
  <?= $flashes ?? '' ?>

  <!-- ESTATÍSTICAS GLOBAIS -->
  <div class="stats-row">
    <div class="stat-card"><div class="stat-num" style="color:#0a3d62;"><?= (int)($stats['total_estudantes'] ?? 0) ?></div><div class="stat-label">👥 Estudantes</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#2e7d32;"><?= (int)($stats['total_questoes'] ?? 0) ?></div><div class="stat-label">📝 Questões</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#e65100;"><?= (int)($stats['total_avaliacoes'] ?? 0) ?></div><div class="stat-label">📊 Avaliações</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#2e7d32;"><?= (int)($stats['questoes_facil'] ?? 0) ?></div><div class="stat-label">🟢 Fáceis</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#e65100;"><?= (int)($stats['questoes_medio'] ?? 0) ?></div><div class="stat-label">🟡 Médias</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#c62828;"><?= (int)($stats['questoes_dificil'] ?? 0) ?></div><div class="stat-label">🔴 Difíceis</div></div>
  </div>

  <!-- AÇÕES RÁPIDAS -->
  <div class="quick-actions">
    <a href="<?= url('admin') ?>" class="btn btn-secondary">🔄 Atualizar</a>
    <a href="<?= url('admin-exportar/estudantes') ?>" class="btn btn-secondary" data-confirm="Exportar dados de todos os estudantes em CSV?">📥 Exportar Estudantes</a>
    <a href="<?= url('admin-exportar/avaliacoes') ?>" class="btn btn-secondary" data-confirm="Exportar dados de todas as avaliações em CSV?">📥 Exportar Avaliações</a>
    <a href="<?= url('admin-exportar/questoes') ?>" class="btn btn-secondary" data-confirm="Exportar todas as questões em CSV?">📥 Exportar Questões</a>
    <a href="<?= url('admin', ['focus' => 'log']) ?>" class="btn btn-secondary <?= !empty($focus_log) ? 'ativo' : '' ?>">📋 Ver Log de Auditoria</a>
  </div>

  <!-- ESTUDANTES -->
  <div class="admin-card">
    <div class="admin-card-header" onclick="toggleCard(this)">
      <span>👥</span><h2>Gestão de Estudantes (<?= (int)($total_estudantes ?? 0) ?>)</h2><span class="toggle open">▶</span>
    </div>
    <div class="admin-card-body">
      <form method="GET" action="<?= url('admin-filtrar') ?>" class="search-form">
        <div class="search-bar">
          <input type="search" name="busca" placeholder="🔍 Buscar por nome, email ou telefone..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" autofocus>
          <button type="submit" class="btn btn-primary">Buscar</button>
          <?php if (!empty($_GET['busca'])): ?>
          <a href="<?= url('admin') ?>" class="btn btn-secondary">Limpar</a>
          <?php endif; ?>
        </div>
      </form>

<?php
  $rotaPag = !empty($_GET['busca']) ? 'admin-filtrar' : 'admin';
  $paramsPag = ['pagina' => '__P__'];
  if (!empty($_GET['busca'])) $paramsPag['busca'] = $_GET['busca'];
  if (isset($_GET['focus'])) $paramsPag['focus'] = $_GET['focus'];
  $urlPag = function($p) use ($rotaPag, $paramsPag) {
    $paramsPag['pagina'] = $p;
    return url($rotaPag, $paramsPag);
  };
?>
      <div class="pagination-bar">
        <span class="pag-info"><?= (int)($total_estudantes ?? 0) ?> estudante(s) — Página <?= (int)($pagina_atual ?? 1) ?> de <?= (int)($total_paginas ?? 1) ?></span>
        <?php if (($total_paginas ?? 1) > 1): ?>
        <div class="pag-buttons">
          <?php if (($pagina_atual ?? 1) > 1): ?>
          <a href="<?= $urlPag(($pagina_atual ?? 1) - 1) ?>" class="pag-btn">‹ Anterior</a>
          <?php endif; ?>
          <?php
            $inicio = max(1, ($pagina_atual ?? 1) - 2);
            $fim = min($total_paginas ?? 1, $inicio + 4);
            for ($i = $inicio; $i <= $fim; $i++):
          ?>
          <?php if ($i == ($pagina_atual ?? 1)): ?>
          <span class="pag-btn ativo"><?= $i ?></span>
          <?php else: ?>
          <a href="<?= $urlPag($i) ?>" class="pag-btn"><?= $i ?></a>
          <?php endif; ?>
          <?php endfor; ?>
          <?php if (($pagina_atual ?? 1) < ($total_paginas ?? 1)): ?>
          <a href="<?= $urlPag(($pagina_atual ?? 1) + 1) ?>" class="pag-btn">Próximo ›</a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>

      <div class="student-list">
        <table>
          <tr>
            <th>#</th><th>Nome</th><th>Email</th><th>📞</th><th>📊 Av.</th><th>📅 Cadastro</th><th>Ações</th>
          </tr>
          <?php if (!empty($estudantes)): ?>
          <?php foreach ($estudantes as $e): ?>
          <tr>
            <td><?= (int)$e['id'] ?></td>
            <td><strong><?= htmlspecialchars($e['nome']) ?></strong></td>
            <td><?= htmlspecialchars($e['email']) ?></td>
            <td><?= htmlspecialchars($e['telefone'] ?? '—') ?></td>
            <td><span class="tag <?= ($stats_avaliacoes_por_estudante[$e['id']] ?? 0) > 0 ? 'pass' : 'warn' ?>"><?= (int)($stats_avaliacoes_por_estudante[$e['id']] ?? 0) ?></span></td>
            <td><?= !empty($e['data_cadastro']) ? substr($e['data_cadastro'], 0, 10) : '—' ?></td>
            <td>
              <div class="action-btns">
                <a href="<?= url('admin-resetar-senha/' . $e['id']) ?>" class="btn-action btn-reset" title="Redefinir senha" data-confirm="Redefinir senha de '<?= htmlspecialchars($e['nome']) ?>'? Uma nova senha será gerada.">🔑</a>
                <a href="<?= url('admin-excluir-estudante/' . $e['id']) ?>" class="btn-action btn-delete" title="Excluir estudante" data-confirm="EXCLUIR permanentemente este estudante e TODAS as suas avaliações? Esta ação NÃO pode ser desfeita!">🗑️</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted);">
            <?php if (!empty($_GET['busca'])): ?>
              Nenhum estudante encontrado para "<strong><?= htmlspecialchars($_GET['busca']) ?></strong>"
            <?php else: ?>
              Nenhum estudante cadastrado ainda.
            <?php endif; ?>
          </td></tr>
          <?php endif; ?>
        </table>
      </div>
    </div>
  </div>

  <!-- QUESTÕES -->
  <div class="admin-card">
    <div class="admin-card-header" onclick="toggleCard(this)">
      <span>📝</span><h2>Distribuição de Questões (<?= (int)($stats['total_questoes'] ?? 0) ?>)</h2><span class="toggle">▶</span>
    </div>
    <div class="admin-card-body hidden">
      <div class="section-title">📊 Por Dificuldade</div>
      <?php $totalQ = max(1, (int)($stats['total_questoes'] ?? 1)); ?>
      <table>
        <tr><th>Nível</th><th>Qtd</th><th>%</th><th>Barra</th></tr>
        <tr><td>🟢 Fácil</td><td><?= (int)($stats['questoes_facil'] ?? 0) ?></td>
          <td><?= round(($stats['questoes_facil'] ?? 0)*100/$totalQ, 1) ?>%</td>
          <td><div class="module-bar"><div class="bar facil" style="width:<?= ($stats['questoes_facil'] ?? 0)*100/$totalQ ?>%"></div></div></td></tr>
        <tr><td>🟡 Médio</td><td><?= (int)($stats['questoes_medio'] ?? 0) ?></td>
          <td><?= round(($stats['questoes_medio'] ?? 0)*100/$totalQ, 1) ?>%</td>
          <td><div class="module-bar"><div class="bar medio" style="width:<?= ($stats['questoes_medio'] ?? 0)*100/$totalQ ?>%"></div></div></td></tr>
        <tr><td>🔴 Difícil</td><td><?= (int)($stats['questoes_dificil'] ?? 0) ?></td>
          <td><?= round(($stats['questoes_dificil'] ?? 0)*100/$totalQ, 1) ?>%</td>
          <td><div class="module-bar"><div class="bar dificil" style="width:<?= ($stats['questoes_dificil'] ?? 0)*100/$totalQ ?>%"></div></div></td></tr>
      </table>

      <div class="section-title">📚 Por Módulo (<?= (int)($stats['total_questoes'] ?? 0) ?> total)</div>
      <table>
        <tr><th>Módulo</th><th>Total</th><th>🟢</th><th>🟡</th><th>🔴</th><th>Barra</th></tr>
        <?php foreach ($questoes_por_modulo as $m): ?>
        <?php $pctMod = round((int)$m['total']*100/$totalQ, 1); ?>
        <tr>
          <td><strong>Módulo <?= (int)$m['modulo'] ?></strong></td>
          <td><?= (int)$m['total'] ?></td>
          <td><?= (int)$m['facil'] ?></td>
          <td><?= (int)$m['medio'] ?></td>
          <td><?= (int)$m['dificil'] ?></td>
          <td><div class="module-bar" style="min-width:100px;">
            <div class="bar facil" style="width:<?= $totalQ>0 ? (int)$m['facil']*100/$totalQ : 0 ?>%;background:#4caf50;"></div>
            <div class="bar medio" style="width:<?= $totalQ>0 ? (int)$m['medio']*100/$totalQ : 0 ?>%;background:#ff9800;"></div>
            <div class="bar dificil" style="width:<?= $totalQ>0 ? (int)$m['dificil']*100/$totalQ : 0 ?>%;background:#f44336;"></div>
          </div></td>
        </tr>
        <?php endforeach; ?>
      </table>

      <div class="section-title">📄 Últimas Questões Adicionadas</div>
      <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
      <table>
        <tr><th>ID</th><th>Módulo</th><th>Dificuldade</th><th>Texto</th><th>Resposta</th></tr>
        <?php foreach ($ultimas_questoes as $q): ?>
        <tr>
          <td><?= (int)$q['id'] ?></td>
          <td><span class="tag info">M<?= (int)$q['modulo'] ?></span></td>
          <td><span class="tag <?= $q['dificuldade']=='Fácil'?'pass':($q['dificuldade']=='Médio'?'warn':'')?>"><?= $q['dificuldade']=='Fácil'?'🟢':($q['dificuldade']=='Médio'?'🟡':'🔴') ?> <?= htmlspecialchars($q['dificuldade']) ?></span></td>
          <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($q['texto']) ?>"><?= htmlspecialchars(mb_substr($q['texto'],0,80)) ?><?= mb_strlen($q['texto'])>80?'...':'' ?></td>
          <td><strong><?= htmlspecialchars($q['resposta']) ?></strong></td>
        </tr>
        <?php endforeach; ?>
      </table>
      </div>
    </div>
  </div>

  <!-- LOG DE AUDITORIA -->
  <div class="admin-card">
    <div class="admin-card-header" onclick="toggleCard(this)">
      <span>📋</span><h2>Log de Auditoria (últimas 50 ações)</h2><span class="toggle <?= !empty($focus_log) ? 'open' : '' ?>">▶</span>
    </div>
    <div class="admin-card-body <?= !empty($focus_log) ? '' : 'hidden' ?>">
      <?php if (!empty($logs)): ?>
      <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
      <table>
        <tr>
          <th>Data/Hora</th>
          <th>Ação</th>
          <th>Detalhes</th>
        </tr>
        <?php foreach ($logs as $log): ?>
        <?php $detalhes = json_decode($log['detalhes'], true) ?? []; ?>
        <tr>
          <td style="white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($log['data'])) ?></td>
          <td>
            <?php
              $icones = [
                'excluir_estudante' => '🗑️',
                'resetar_senha' => '🔑',
                'exportar_csv' => '📥',
              ];
              $icon = $icones[$log['acao']] ?? '🔹';
            ?>
            <span class="tag info"><?= $icon ?> <?= htmlspecialchars($log['acao']) ?></span>
          </td>
          <td style="font-size:0.82em;color:var(--text-light);">
            <?php if (!empty($detalhes)): ?>
              <?php foreach ($detalhes as $chave => $valor): ?>
                <strong style="color:var(--text);"><?= htmlspecialchars($chave) ?>:</strong> <?= htmlspecialchars(is_array($valor) ? json_encode($valor) : $valor) ?> 
              <?php endforeach; ?>
            <?php else: ?>
              <span class="text-muted">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <div class="empty-icon">📋</div>
        <p>Nenhuma ação administrativa registrada ainda.</p>
        <p class="empty-sub">As ações de excluir/resetar estudantes e exportar dados serão registradas aqui.</p>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- INFORMAÇÕES DO SISTEMA -->
  <div class="admin-card">
    <div class="admin-card-header" onclick="toggleCard(this)">
      <span>ℹ️</span><h2>Informações do Sistema</h2><span class="toggle">▶</span>
    </div>
    <div class="admin-card-body hidden">
      <div class="info-grid">
        <div class="info-item"><div class="info-label">Disciplina</div><div class="info-value">Introdução à Enfermagem (EN_430)</div></div>
        <div class="info-item"><div class="info-label">Framework</div><div class="info-value">PHP 8.4+ (PDO SQLite) + JavaScript</div></div>
        <div class="info-item"><div class="info-label">Hash de Senhas</div><div class="info-value">bcrypt (password_hash, cost 12)</div></div>
        <div class="info-item"><div class="info-label">CSRF</div><div class="info-value">Tokens de sessão (SHA-256)</div></div>
        <div class="info-item"><div class="info-label">Versão</div><div class="info-value">2.0 (PHP + JS)</div></div>
        <div class="info-item"><div class="info-label">Testes</div><div class="info-value">158 PHPUnit (54% coverage)</div></div>
      </div>
      <div style="margin-top:15px;text-align:center;font-size:0.85em;color:var(--text-muted);">
        📋 <strong>Rotas:</strong> / /cadastro /login /painel /avaliacao/{id} /progresso /admin /admin-login /admin?focus=log /recuperar-acesso
      </div>
    </div>
  </div>
</div>

<script src="<?= url('assets/js/app.js') ?>"></script>
<script>
function toggleCard(header) {
  var body = header.nextElementSibling;
  if (body) {
    body.classList.toggle('hidden');
    var toggle = header.querySelector('.toggle');
    if (toggle) toggle.classList.toggle('open');
  }
}
</script>
</body>
</html>
