<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acesso Administrativo</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body class="admin-login-page">
<div class="admin-login-box">
  <div class="admin-login-icon">🔒</div>
  <h2>⚙️ Acesso Administrativo</h2>
  <p>Informe a senha de administração para acessar o painel de controle do sistema.</p>

  <?= $flashes ?? '' ?>

  <form method="POST" action="<?= url('admin-login') ?>">
    <?= getCsrfField() ?>
    <input type="password" name="senha_admin" placeholder="Senha de administração" autofocus>
    <button type="submit" class="btn btn-primary btn-full">🔑 Acessar Painel</button>
  </form>
  <a href="<?= url('') ?>" class="admin-back-link">← Voltar ao Sistema</a>
</div>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
