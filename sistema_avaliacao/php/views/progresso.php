<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Progresso - Sistema de Avaliação</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<div class="topbar">
  <h1>📊 Meu Progresso</h1>
  <a href="<?= url('painel') ?>" class="btn-sair">← Voltar ao Painel</a>
</div>
<div class="container">
  <?= $flashes ?? '' ?>

  <?php $icones = ['🧩','🔄','📝','💊','💉','🧼','🩹','🫁','📏','📋']; ?>
  <?php $modulos = ['Teoria das Necessidades Básicas','SAE','Anotação de Enfermagem','Medicação Vias Enterais','Vias Parenterais','Assepsia e Higiene','Curativos e Feridas','Oxigenoterapia e Cateterismos','Exames e Medidas','Admissão, Alta e Pós-Morte']; ?>
  <?php $cores = ['verde','laranja','vermelho','azul']; ?>
  <?php $dificores = ['Fácil' => ['icon' => '🟢', 'cor' => 'verde'], 'Médio' => ['icon' => '🟡', 'cor' => 'laranja'], 'Difícil' => ['icon' => '🔴', 'cor' => 'vermelho']]; ?>

  <?php if (!empty($stats_dificuldade)): ?>
  <div class="section">
    <div class="section-header">📊 Desempenho por Dificuldade</div>
    <div class="dif-grid">
      <?php foreach ($stats_dificuldade as $item): ?>
        <?php $info = $dificores[$item['dificuldade']] ?? ['icon' => '📚', 'cor' => 'azul']; ?>
        <?php $pct = ($item['total_respondidas_real'] ?? 0) > 0 ? round(($item['acertos'] ?? 0) * 100 / $item['total_respondidas_real']) : 0; ?>
        <div class="dif-card dif-<?= $info['cor'] ?>">
          <div class="dif-icon"><?= $info['icon'] ?></div>
          <div class="dif-label"><?= htmlspecialchars($item['dificuldade']) ?></div>
          <div class="dif-pct"><?= $pct ?>%</div>
          <div class="dif-detail"><?= (int)($item['acertos'] ?? 0) ?>/<?= (int)($item['total_respondidas_real'] ?? 0) ?> acertos</div>
          <div class="dif-detail"><?= (int)($item['respondidas'] ?? 0) ?>/<?= (int)($item['total_banco'] ?? 0) ?> questões</div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="section">
    <div class="section-header">
      <span>📈 Filtrar por Dificuldade</span>
      <div class="filter-bar">
        <a href="<?= url('progresso', ['dificuldade' => 'todas']) ?>" class="filter-btn <?= ($filtro_atual ?? 'todas') == 'todas' ? 'ativo' : '' ?> btn-todas">📚 Todas</a>
        <a href="<?= url('progresso', ['dificuldade' => 'Fácil']) ?>" class="filter-btn <?= ($filtro_atual ?? '') == 'Fácil' ? 'ativo' : '' ?> btn-facil">🟢 Fácil</a>
        <a href="<?= url('progresso', ['dificuldade' => 'Médio']) ?>" class="filter-btn <?= ($filtro_atual ?? '') == 'Médio' ? 'ativo' : '' ?> btn-medio">🟡 Médio</a>
        <a href="<?= url('progresso', ['dificuldade' => 'Difícil']) ?>" class="filter-btn <?= ($filtro_atual ?? '') == 'Difícil' ? 'ativo' : '' ?> btn-dificil">🔴 Difícil</a>
      </div>
    </div>
    <div class="filtro-info">
      <?php if (!empty($filtro_atual) && $filtro_atual !== 'todas'): ?>
        Filtrando por: <strong><?= $filtro_nome ?? '' ?></strong>
      <?php else: ?>
        Mostrando todos os níveis
      <?php endif; ?>
    </div>
  </div>

  <?php if (!empty($acertos_modulos)): ?>
  <div class="section">
    <div class="section-header">📈 Desempenho por Módulo</div>
    <div class="modulo-grid">
      <?php foreach ($acertos_modulos as $item): ?>
        <?php $pct = ($item['total_respondidas'] ?? 0) > 0 ? round(($item['acertos'] ?? 0) * 100 / $item['total_respondidas']) : 0; ?>
        <div class="modulo-card">
          <h3><?= $icones[($item['modulo'] ?? 1) - 1] ?? '📚' ?> Módulo <?= (int)($item['modulo'] ?? 0) ?>: <?= $modulos[($item['modulo'] ?? 1) - 1] ?? '' ?></h3>
          <div class="bar">
            <div class="bar-fill" style="width: <?= $pct ?>%; background: <?= $pct >= 70 ? '#4caf50' : ($pct >= 50 ? '#ff9800' : '#f44336') ?>;"></div>
          </div>
          <div class="bar-stats">
            <span><?= (int)($item['acertos'] ?? 0) ?>/<?= (int)($item['total_respondidas'] ?? 0) ?> acertos</span>
            <span class="bar-pct" style="color: <?= $pct >= 70 ? '#4caf50' : ($pct >= 50 ? '#ff9800' : '#f44336') ?>;"><?= $pct ?>%</span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="section">
    <div class="section-header">📚 Questões Respondidas por Módulo</div>
    <div class="modulo-grid">
      <?php foreach ($progresso_modulos as $item): ?>
        <?php $pct = ($item['total_questoes'] ?? 0) > 0 ? round(($item['respondidas'] ?? 0) * 100 / $item['total_questoes']) : 0; ?>
        <div class="modulo-card">
          <h3><?= $icones[($item['modulo'] ?? 1) - 1] ?? '📚' ?> Módulo <?= (int)($item['modulo'] ?? 0) ?></h3>
          <div class="bar">
            <div class="bar-fill" style="width: <?= $pct ?>%; background: #0a3d62;"></div>
          </div>
          <div class="bar-stats">
            <span><?= (int)($item['respondidas'] ?? 0) ?>/<?= (int)($item['total_questoes'] ?? 0) ?> respondidas</span>
            <span class="bar-pct" style="color:#0a3d62;"><?= $pct ?>%</span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php else: ?>
  <div class="empty-state">
    <div class="empty-icon">📊</div>
    <p>Você ainda não realizou nenhuma avaliação.</p>
    <p class="empty-sub">Complete uma avaliação para ver seu progresso detalhado por módulo!</p>
    <form action="<?= url('nova-avaliacao') ?>" method="POST" style="margin-top:15px;">
      <?= getCsrfField() ?>
      <button type="submit" class="btn btn-primary">🎲 Iniciar Primeira Avaliação</button>
    </form>
  </div>
  <?php endif; ?>
</div>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
