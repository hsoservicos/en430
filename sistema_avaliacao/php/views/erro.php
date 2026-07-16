<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Erro - Sistema de Avaliação EN_430</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
<style>
.error-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 80vh;
  padding: 20px;
  text-align: center;
}
.error-card {
  background: var(--card);
  border-radius: 20px;
  padding: 50px 40px;
  max-width: 500px;
  width: 100%;
  box-shadow: 0 20px 60px rgba(0,0,0,0.1);
}
.error-icon {
  font-size: 5em;
  margin-bottom: 15px;
  animation: float 3s ease-in-out infinite;
}
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
.error-code {
  font-size: 4em;
  font-weight: 900;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1;
}
.error-title {
  font-size: 1.4em;
  color: #1a1a2e;
  margin: 10px 0 8px;
}
.error-desc {
  color: var(--text-light);
  font-size: 0.9em;
  margin-bottom: 25px;
  line-height: 1.6;
}
.error-actions {
  display: flex;
  gap: 10px;
  justify-content: center;
  flex-wrap: wrap;
}
.error-actions .btn {
  padding: 12px 28px;
  font-size: 0.9em;
}
@media (max-width: 500px) {
  .error-card { padding: 30px 20px; }
  .error-icon { font-size: 3.5em; }
  .error-code { font-size: 3em; }
}
</style>
</head>
<body>
<div class="error-page">
  <div class="error-card">
    <?php if (!empty($codigo) && $codigo === 404): ?>
    <div class="error-icon">🔍</div>
    <div class="error-code">404</div>
    <div class="error-title">Página não encontrada</div>
    <div class="error-desc">
      A página que você procura não existe ou foi movida.<br>
      Verifique o endereço ou volte para o início.
    </div>
    <?php else: ?>
    <div class="error-icon">⚠️</div>
    <div class="error-code">500</div>
    <div class="error-title">Erro interno do servidor</div>
    <div class="error-desc">
      Ocorreu um erro inesperado. Nossa equipe foi notificada.<br>
      Tente novamente em alguns instantes.
    </div>
    <?php endif; ?>
    <div class="error-actions">
      <a href="<?= url('') ?>" class="btn btn-primary">🏠 Página Inicial</a>
      <?php if (!empty($_SESSION['estudante_id'])): ?>
      <a href="<?= url('painel') ?>" class="btn btn-secondary">📋 Meu Painel</a>
      <?php else: ?>
      <a href="<?= url('login') ?>" class="btn btn-secondary">🔑 Fazer Login</a>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
