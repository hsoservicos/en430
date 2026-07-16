<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resultado - Sistema de Avaliação</title>
<?php $pct = ($avaliacao['total_questoes'] ?? 0) > 0 ? round(($avaliacao['pontuacao'] ?? 0) * 100 / $avaliacao['total_questoes']) : 0; ?>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<div class="container">
  <div class="result-header" style="background: linear-gradient(135deg, <?= $pct >= 70 ? '#1b5e20,#388e3c' : ($pct >= 50 ? '#e65100,#f57c00' : '#b71c1c,#d32f2f') ?>);">
    <div class="nota-grande"><?= $pct ?>%</div>
    <div class="nota-msg"><?= badgeNota($pct) ?></div>
    <div class="result-info">
      <span>📅 <?= dataHoraBrasil($avaliacao['data_fim'] ?? '') ?></span>
      <span>❓ <?= (int)($avaliacao['total_questoes'] ?? 0) ?> questões</span>
      <span>✅ <?= (int)($avaliacao['pontuacao'] ?? 0) ?> acertos</span>
    </div>
  </div>

  <div class="actions">
    <form action="<?= url('nova-avaliacao') ?>" method="POST" style="display:inline;">
      <?= getCsrfField() ?>
      <button type="submit" class="btn btn-primary">🎲 Nova Avaliação</button>
    </form>
    <a href="<?= url('painel') ?>" class="btn btn-secondary">📋 Ver Histórico</a>
    <a href="<?= url('progresso') ?>" class="btn btn-secondary">📊 Meu Progresso</a>
  </div>

  <div class="resumo">
    <div class="resumo-item">
      <div class="resumo-num certa"><?= (int)($avaliacao['pontuacao'] ?? 0) ?></div>
      <div class="resumo-label">✅ Acertos</div>
    </div>
    <div class="resumo-item">
      <div class="resumo-num errada"><?= (int)(($avaliacao['total_questoes'] ?? 0) - ($avaliacao['pontuacao'] ?? 0)) ?></div>
      <div class="resumo-label">❌ Erros</div>
    </div>
    <div class="resumo-item">
      <div class="resumo-num"><?= $pct ?>%</div>
      <div class="resumo-label">📊 Aproveitamento</div>
    </div>
  </div>

  <?php $indice = 1; ?>
  <?php foreach ($questoes_ids as $qid): ?>
    <?php $qr = $resultado[(string)$qid] ?? null; ?>
    <?php if ($qr): ?>
    <div class="questao-result" style="border-left-color: <?= $qr['correta'] ? '#4caf50' : '#f44336' ?>;">
      <div class="qr-header">
        <span class="qr-num">
          Questão <?= $indice ?>
          <span class="mod-badge">Módulo <?= (int)$qr['modulo'] ?></span>
          <span class="dif-badge dif-<?= strtolower($qr['dificuldade'] ?? 'medio') ?>">
            <?php if (($qr['dificuldade'] ?? 'Médio') == 'Fácil'): ?>🟢 Fácil
            <?php elseif (($qr['dificuldade'] ?? 'Médio') == 'Difícil'): ?>🔴 Difícil
            <?php else: ?>🟡 Médio<?php endif; ?>
          </span>
        </span>
        <span class="qr-status <?= $qr['correta'] ? 'acertou' : 'errou' ?>">
          <?= $qr['correta'] ? '✅ Correta' : '❌ Incorreta' ?>
        </span>
      </div>
      <div class="qr-texto"><?= htmlspecialchars($qr['texto'] ?? '') ?></div>
      <div class="qr-respostas">
        <?php if (!empty($qr['resposta_estudante'])): ?>
        <div class="sua-resp <?= !$qr['correta'] ? 'errada' : '' ?>">
          <strong>Sua resposta:</strong> <?= htmlspecialchars($qr['resposta_estudante']) ?>) 
          <?= htmlspecialchars($qr['opcoes'][$qr['resposta_estudante']] ?? '') ?>
        </div>
        <?php else: ?>
        <div class="sua-resp errada"><strong>Você não respondeu esta questão.</strong></div>
        <?php endif; ?>
        <?php if (!$qr['correta']): ?>
        <div class="resp-correta">
          <strong>Resposta correta:</strong> <?= htmlspecialchars($qr['resposta_correta']) ?>) 
          <?= htmlspecialchars($qr['opcoes'][$qr['resposta_correta']] ?? '') ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
    <?php $indice++; ?>
  <?php endforeach; ?>
</div>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
