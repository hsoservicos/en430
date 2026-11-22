<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro - Sistema de Avaliação</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body class="auth-page">
<div class="auth-card">
  <h1>📝 Cadastro</h1>
  <p>Crie sua conta para acessar o sistema de avaliação</p>
  
  <?= $flashes ?? '' ?>
  
  <form method="POST" action="<?= url('cadastro') ?>" data-enter-submit>
    <?= getCsrfField() ?>
    <div class="form-group">
      <label for="nome">Nome completo</label>
      <input type="text" id="nome" name="nome" required data-autofocus
             value="<?= htmlspecialchars($dados['nome'] ?? '') ?>" placeholder="Seu nome completo">
    </div>
    <div class="form-group">
      <label for="data_nascimento">📅 Data de Nascimento <span class="optional">(opcional)</span></label>
      <input type="text" id="data_nascimento" name="data_nascimento" data-mask="data"
             value="<?= htmlspecialchars($dados['data_nascimento'] ?? '') ?>" placeholder="DD/MM/AAAA">
    </div>
    <div class="form-group">
      <label for="telefone">📞 Telefone <span class="optional">(opcional)</span></label>
      <input type="text" id="telefone" name="telefone" data-mask="telefone"
             value="<?= htmlspecialchars($dados['telefone'] ?? '') ?>" placeholder="(XX) XXXXX-XXXX">
    </div>
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required
             value="<?= htmlspecialchars($dados['email'] ?? '') ?>" placeholder="seu@email.com">
    </div>
    <div class="form-group">
      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" required minlength="4" placeholder="Mínimo 4 caracteres">
    </div>
    <div class="form-group">
      <label for="confirmar_senha">Confirmar senha</label>
      <input type="password" id="confirmar_senha" name="confirmar_senha" required placeholder="Repita a senha">
    </div>
    <div class="form-group form-consent">
      <label class="consent-label" for="consentimento">
        <input type="checkbox" id="consentimento" name="consentimento" value="1" required
               <?= !empty($dados['consentimento']) ? 'checked' : '' ?>>
        <span>
          Li e concordo com o
          <a href="<?= url('politica-de-privacidade') ?>" target="_blank" rel="noopener">Aviso de
          Tratamento de Dados e a Política de Privacidade</a>, e autorizo o tratamento dos meus dados
          (nome, e-mail, senha e, se informados, data de nascimento e telefone) para criar e manter
          minha conta, corrigir avaliações e exibir meu desempenho.
        </span>
      </label>
    </div>

    <button type="submit" class="btn btn-primary btn-full">📝 Criar Conta</button>
  </form>
  <div class="auth-links">
    Já tem conta? <a href="<?= url('login') ?>">Faça login</a>
  </div>
</div>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
