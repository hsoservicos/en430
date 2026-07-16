<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar Acesso - Sistema de Avaliação</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body class="auth-page">
<div class="auth-card">
  <h1>🔐 Recuperar Acesso</h1>
  <p>Informe seu <strong>Nome</strong> e <strong>Telefone</strong> cadastrados para recuperar o acesso à sua conta.</p>

  <?= $flashes ?? '' ?>

  <?php if (!empty($encontrado)): ?>
    <div class="info-box success">
      <strong>✅ Conta Encontrada!</strong><br>
      Seu email de acesso é: <strong><?= htmlspecialchars($email ?? '') ?></strong><br>
      Agora defina uma nova senha para continuar.
    </div>

    <div class="section-title">🔑 Redefinir Senha</div>
    <form method="POST" action="<?= url('redefinir-senha') ?>" data-enter-submit>
      <?= getCsrfField() ?>
      <input type="hidden" name="nome" value="<?= htmlspecialchars($nome ?? '') ?>">
      <input type="hidden" name="telefone" value="<?= htmlspecialchars($telefone ?? '') ?>">
      <div class="form-group">
        <label for="nova_senha">🔒 Nova Senha</label>
        <input type="password" id="nova_senha" name="nova_senha" required data-autofocus
               minlength="4" placeholder="Mínimo 4 caracteres">
        <div class="requisitos">Mínimo de 4 caracteres</div>
      </div>
      <div class="form-group">
        <label for="confirmar_senha">✅ Confirmar Nova Senha</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required
               minlength="4" placeholder="Repita a nova senha">
      </div>
      <button type="submit" class="btn btn-success btn-full">💾 Salvar Nova Senha</button>
    </form>
    <div class="auth-links">
      <a href="<?= url('login') ?>">← Voltar ao Login</a>
    </div>

  <?php else: ?>
    <form method="POST" action="<?= url('recuperar-acesso') ?>" data-enter-submit>
      <?= getCsrfField() ?>
      <div class="form-group">
        <label for="nome">👤 Nome Completo</label>
        <input type="text" id="nome" name="nome" required data-autofocus
               placeholder="Seu nome cadastrado">
      </div>
      <div class="form-group">
        <label for="telefone">📞 Telefone</label>
        <input type="text" id="telefone" name="telefone" required data-mask="telefone"
               placeholder="(XX) XXXXX-XXXX">
      </div>
      <button type="submit" class="btn btn-primary btn-full">🔍 Buscar Minha Conta</button>
    </form>
    <div class="auth-links">
      <a href="<?= url('login') ?>">← Voltar ao Login</a>
    </div>
  <?php endif; ?>
</div>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
