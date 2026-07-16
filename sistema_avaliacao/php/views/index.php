<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema de Avaliação - Introdução à Enfermagem</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<div class="hero">
  <h1>📝 Sistema de Avaliação</h1>
  <p>Introdução à Enfermagem (EN_430) — +3000 questões de múltipla escolha</p>
  <div class="hero-info">
    <span>📚 10 módulos</span>
    <span>❓ +3000 questões</span>
    <span>🎲 20 questões por avaliação</span>
    <span>📊 Acompanhe seu progresso</span>
  </div>
</div>
<div class="container">
  <?= $flashes ?? '' ?>
  
  <div class="card">
    <h2>🧑‍🎓 Bem-vindo ao Sistema de Avaliação!</h2>
    <p>
      Este sistema permite que você <strong>teste seus conhecimentos</strong> em Introdução à Enfermagem 
      com questões de múltipla escolha geradas <strong>aleatoriamente</strong> a partir de um banco 
      com <strong>mais de 3000 questões</strong> distribuídas pelos 10 módulos da disciplina.
    </p>
    <div class="btn-group">
      <a href="<?= url('login') ?>" class="btn btn-primary">🔑 Fazer Login</a>
      <a href="<?= url('cadastro') ?>" class="btn btn-secondary">📝 Cadastre-se</a>
    </div>
  </div>
  
  <div class="features">
    <div class="feature">
      <div class="feature-icon">🎲</div>
      <h3>Avaliações Aleatórias</h3>
      <p>20 questões únicas por avaliação, sem repetição, com filtro por dificuldade</p>
    </div>
    <div class="feature">
      <div class="feature-icon">📊</div>
      <h3>Acompanhamento</h3>
      <p>Histórico completo e gráficos de desempenho por módulo e dificuldade</p>
    </div>
    <div class="feature">
      <div class="feature-icon">✅</div>
      <h3>Correção Automática</h3>
      <p>Resultado imediato com gabarito detalhado questão por questão</p>
    </div>
    <div class="feature">
      <div class="feature-icon">📈</div>
      <h3>Progresso por Módulo</h3>
      <p>Veja seu desempenho em cada um dos 10 módulos da disciplina</p>
    </div>
  </div>
  
  <div class="card" style="text-align:left;">
    <h3>📋 Como funciona</h3>
    <ol class="steps">
      <li><strong>Cadastre-se</strong> com seu nome e email</li>
      <li><strong>Faça login</strong> e acesse seu painel personalizado</li>
      <li><strong>Escolha a dificuldade</strong> — Fácil, Médio, Difícil ou Todas</li>
      <li><strong>Inicie uma avaliação</strong> — 20 questões são selecionadas aleatoriamente</li>
      <li><strong>Responda</strong> e receba o resultado imediato com gabarito</li>
      <li><strong>Acompanhe seu progresso</strong> — veja sua evolução por módulo e dificuldade</li>
    </ol>
  </div>
</div>
<footer>
  📚 Sistema de Avaliação • Introdução à Enfermagem (EN_430) • +3000 questões • Julho 2026
</footer>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
