<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Sistema de Avaliação</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body class="auth-page">
<div class="auth-card">
  <h1>🔑 Login</h1>
  <p>Acesse sua conta para continuar estudando</p>
  
  <?= $flashes ?? '' ?>
  
  <form method="POST" action="<?= url('login') ?>" data-enter-submit>
    <?= getCsrfField() ?>
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required data-autofocus placeholder="seu@email.com">
    </div>
    <div class="form-group">
      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" required placeholder="Sua senha">
    </div>
    <button type="submit" class="btn btn-primary btn-full">🔑 Entrar</button>
  </form>
  <div class="auth-links">
    <a href="<?= url('recuperar-acesso') ?>" class="forgot-link">🔐 Esqueci minha senha</a>
    <span>Não tem conta? <a href="<?= url('cadastro') ?>">Cadastre-se</a></span>
  </div>
</div>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
