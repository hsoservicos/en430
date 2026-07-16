<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel - Sistema de Avaliação</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<div class="topbar">
  <h1>📝 Sistema de Avaliação</h1>
  <div class="topbar-user">
    <span>👤 <?= htmlspecialchars($estudante['nome'] ?? '') ?></span>
    <span>📧 <?= htmlspecialchars($estudante['email'] ?? '') ?></span>
    <?php if (!empty($estudante['data_nascimento'])): ?>
    <span>🎂 <?= htmlspecialchars($estudante['data_nascimento']) ?></span>
    <?php endif; ?>
    <a href="<?= url('logout') ?>" class="btn-sair">🚪 Sair</a>
  </div>
</div>

<div class="container">
  <?= $flashes ?? '' ?>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-num"><?= (int)($stats['total_avaliacoes'] ?? 0) ?></div>
      <div class="stat-label">Avaliações Realizadas</div>
    </div>
    <div class="stat-card">
      <div class="stat-num"><?= round($stats['media_acertos'] ?? 0) ?>%</div>
      <div class="stat-label">Média de Acertos</div>
    </div>
    <div class="stat-card destaque">
      <div class="stat-num"><?= round($stats['melhor_nota'] ?? 0) ?>%</div>
      <div class="stat-label">Melhor Desempenho</div>
    </div>
    <div class="stat-card">
      <div class="stat-num"><?= (int)($stats['em_andamento'] ?? 0) ?></div>
      <div class="stat-label">Em Andamento</div>
    </div>
  </div>

  <div class="filter-card">
    <div class="filter-title">🎯 Selecione o nível de dificuldade</div>
    <form action="<?= url('nova-avaliacao') ?>" method="POST" class="filter-form">
      <?= getCsrfField() ?>
      <div class="filter-options" role="radiogroup">
        <label class="filter-opt selected" tabindex="0"
               onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}"
               onclick="selecionarDificuldade(this, 'todas')">
          <input type="radio" name="dificuldade" value="todas" checked>
          <span class="opt-icon">📚</span>
          <span class="opt-label">Todas</span>
          <span class="opt-sub">Misturado</span>
        </label>
        <label class="filter-opt" tabindex="0"
               onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}"
               onclick="selecionarDificuldade(this, 'Fácil')">
          <input type="radio" name="dificuldade" value="Fácil">
          <span class="opt-icon">🟢</span>
          <span class="opt-label">Fácil</span>
          <span class="opt-sub">Conceitos básicos</span>
        </label>
        <label class="filter-opt" tabindex="0"
               onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}"
               onclick="selecionarDificuldade(this, 'Médio')">
          <input type="radio" name="dificuldade" value="Médio">
          <span class="opt-icon">🟡</span>
          <span class="opt-label">Médio</span>
          <span class="opt-sub">Aplicação clínica</span>
        </label>
        <label class="filter-opt" tabindex="0"
               onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}"
               onclick="selecionarDificuldade(this, 'Difícil')">
          <input type="radio" name="dificuldade" value="Difícil">
          <span class="opt-icon">🔴</span>
          <span class="opt-label">Difícil</span>
          <span class="opt-sub">Raciocínio crítico</span>
        </label>
      </div>
      <button type="submit" class="btn btn-primary btn-start" data-autofocus>🎲 Iniciar Avaliação</button>
    </form>
  </div>

  <div class="action-bar">
    <a href="<?= url('progresso') ?>" class="btn btn-secondary">📊 Ver Progresso Detalhado</a>
  </div>

  <div class="section">
    <div class="section-header">📋 Histórico de Avaliações</div>
    <div class="section-body">
      <?php if (!empty($avaliacoes)): ?>
      <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Data</th>
            <th>Questões</th>
            <th>Acertos</th>
            <th>Nota</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($avaliacoes as $av): ?>
          <?php $pct = $av['total_questoes'] > 0 ? round($av['pontuacao'] * 100 / $av['total_questoes']) : 0; ?>
          <tr>
            <td><?= dataHoraBrasil($av['data_inicio'] ?? '') ?></td>
            <td><?= (int)$av['total_questoes'] ?></td>
            <td><?= $av['pontuacao'] !== null ? "{$av['pontuacao']}/{$av['total_questoes']}" : '-' ?></td>
            <td>
              <?php if ($av['pontuacao'] !== null): ?>
              <span class="nota <?= $pct >= 70 ? 'alta' : ($pct >= 50 ? 'media' : 'baixa') ?>">
                <?= $pct ?>%
              </span>
              <?php else: ?>
              -
              <?php endif; ?>
            </td>
            <td>
              <?php if ($av['status'] === 'concluido'): ?>
                <span class="status status-done">✅ Concluído</span>
              <?php else: ?>
                <span class="status status-pending">⏳ Em andamento</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($av['status'] === 'concluido'): ?>
                <a href="<?= url('avaliacao/' . $av['id'] . '/resultado') ?>" class="table-link">Ver →</a>
              <?php else: ?>
                <a href="<?= url('avaliacao/' . $av['id']) ?>" class="table-link">Continuar →</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <div class="empty-icon">📝</div>
        <p>Nenhuma avaliação realizada ainda.</p>
        <p class="empty-sub">Escolha uma dificuldade acima e clique em "Iniciar Avaliação" para começar!</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="<?= url('assets/js/app.js') ?>"></script>
<script>
function selecionarDificuldade(el, valor) {
  document.querySelectorAll('.filter-opt').forEach(e => e.classList.remove('selected'));
  el.classList.add('selected');
  el.querySelector('input').checked = true;
}
</script>
</body>
</html>
