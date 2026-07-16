<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Avaliação - Sistema de Avaliação</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<div class="container">
  <div class="av-header">
    <div>
      <h1>📝 Respondendo Avaliação</h1>
      <div class="av-meta">
        <span>📅 <?= dataHoraBrasil($avaliacao['data_inicio'] ?? '') ?></span>
        <span>❓ <?= count($questoes) ?> questões</span>
        <span id="respondidas">✅ 0 respondidas</span>
      </div>
    </div>
    <a href="<?= url('painel') ?>" class="btn btn-secondary" style="background:rgba(255,255,255,0.15);color:#fff;">← Voltar</a>
  </div>

  <form id="form-avaliacao" action="<?= url('avaliacao/' . $avaliacao['id'] . '/responder') ?>" method="POST" data-enter-submit>
    <?= getCsrfField() ?>
    
    <?php $indice = 1; ?>
    <?php foreach ($questoes as $questao): ?>
    <div class="questao-card" data-qid="<?= $questao['id'] ?>">
      <div class="q-header">
        <span class="q-badge">Q<?= $indice ?></span>
        <span class="q-modulo">Módulo <?= (int)$questao['modulo'] ?></span>
        <span class="dif-badge dif-<?= strtolower($questao['dificuldade'] ?? 'medio') ?>">
          <?php if (($questao['dificuldade'] ?? 'Médio') == 'Fácil'): ?>🟢 Fácil
          <?php elseif (($questao['dificuldade'] ?? 'Médio') == 'Difícil'): ?>🔴 Difícil
          <?php else: ?>🟡 Médio<?php endif; ?>
        </span>
      </div>
      <div class="q-texto"><?= htmlspecialchars($questao['texto']) ?></div>
      <ul class="q-opcoes">
        <li onclick="selecionarOpcao(this, '<?= $questao['id'] ?>', 'A')">
          <input type="radio" name="q_<?= $questao['id'] ?>" value="A" id="q<?= $questao['id'] ?>_A" onchange="marcarRadio(this)">
          <span class="letra">A)</span> <?= htmlspecialchars($questao['opcao_a']) ?>
        </li>
        <li onclick="selecionarOpcao(this, '<?= $questao['id'] ?>', 'B')">
          <input type="radio" name="q_<?= $questao['id'] ?>" value="B" id="q<?= $questao['id'] ?>_B" onchange="marcarRadio(this)">
          <span class="letra">B)</span> <?= htmlspecialchars($questao['opcao_b']) ?>
        </li>
        <li onclick="selecionarOpcao(this, '<?= $questao['id'] ?>', 'C')">
          <input type="radio" name="q_<?= $questao['id'] ?>" value="C" id="q<?= $questao['id'] ?>_C" onchange="marcarRadio(this)">
          <span class="letra">C)</span> <?= htmlspecialchars($questao['opcao_c']) ?>
        </li>
        <li onclick="selecionarOpcao(this, '<?= $questao['id'] ?>', 'D')">
          <input type="radio" name="q_<?= $questao['id'] ?>" value="D" id="q<?= $questao['id'] ?>_D" onchange="marcarRadio(this)">
          <span class="letra">D)</span> <?= htmlspecialchars($questao['opcao_d']) ?>
        </li>
      </ul>
      <div class="q-progresso">
        <div class="q-progresso-fill" id="progress-<?= $questao['id'] ?>"></div>
      </div>
    </div>
    <?php $indice++; ?>
    <?php endforeach; ?>

    <div class="av-footer">
      <div class="q-counter">
        <span id="counter-display">0</span> de <?= count($questoes) ?> questões respondidas
      </div>
      <button type="submit" class="btn btn-primary btn-lg"
              onclick="return confirm('Tem certeza que deseja finalizar a avaliação? Questões não respondidas serão consideradas erradas.')">
        ✅ Finalizar Avaliação
      </button>
    </div>
  </form>
</div>

<script src="<?= url('assets/js/app.js') ?>"></script>
<script>
function selecionarOpcao(el, qid, valor) {
  const parent = el.parentElement;
  parent.querySelectorAll('li').forEach(li => li.classList.remove('selected'));
  el.classList.add('selected');
  const radio = el.querySelector('input[type="radio"]');
  if (radio) radio.checked = true;
  atualizarContador();
}

function marcarRadio(el) {
  const li = el.closest('li');
  if (li) {
    li.parentElement.querySelectorAll('li').forEach(l => l.classList.remove('selected'));
    li.classList.add('selected');
  }
  atualizarContador();
}

function atualizarContador() {
  const total = document.querySelectorAll('input[type="radio"]:checked').length;
  document.getElementById('counter-display').textContent = total;
  document.getElementById('respondidas').textContent = '✅ ' + total + ' respondidas';
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('input[type="radio"]:checked').forEach(function(radio) {
    const li = radio.closest('li');
    if (li) li.classList.add('selected');
  });
  atualizarContador();
  
  const primeiraOpcao = document.querySelector('.questao-card li');
  if (primeiraOpcao) setTimeout(function() { primeiraOpcao.focus(); }, 300);
});
</script>
</body>
</html>
