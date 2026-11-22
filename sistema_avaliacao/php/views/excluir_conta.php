<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Excluir minha conta - Sistema de Avaliação</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<div class="topbar">
  <h1>🗑️ Excluir minha conta</h1>
  <div class="topbar-user">
    <span>👤 <?= htmlspecialchars(getEstudanteNome()) ?></span>
    <a href="<?= url('painel') ?>" class="btn-sair">← Voltar</a>
  </div>
</div>

<div class="container container-narrow">
  <?= $flashes ?? '' ?>

  <div class="card danger-card">
    <h2>Esta ação é definitiva</h2>
    <p>
      A exclusão é <strong>imediata e irreversível</strong>. Você perderá o acesso à conta e todo o
      histórico de avaliações. Se quiser apenas sair, use
      <a href="<?= url('logout') ?>">Sair</a>.
    </p>

    <table class="tabela-simples">
      <thead>
        <tr><th>O que acontece</th><th>Prazo</th></tr>
      </thead>
      <tbody>
        <tr>
          <td>Conta (nome, e-mail, telefone, data de nascimento) e senha são eliminadas</td>
          <td>Imediato</td>
        </tr>
        <tr>
          <td>Suas avaliações, respostas e pontuações são eliminadas</td>
          <td>Imediato</td>
        </tr>
        <tr>
          <td>Registro de auditoria da exclusão é mantido (sem uso para outra finalidade)</td>
          <td>5 anos</td>
        </tr>
        <tr>
          <td>Registros de acesso do servidor, exigidos pelo Marco Civil</td>
          <td>6 meses</td>
        </tr>
      </tbody>
    </table>

    <p class="texto-pequeno">
      A Política de Privacidade prevê eliminação em até 30 dias após o pedido; nesta versão a
      exclusão é feita na hora, dentro desse prazo. O prazo total está descrito em
      <a href="<?= url('politica-de-privacidade') ?>" target="_blank" rel="noopener">Política de Privacidade</a>,
      itens 8 e 10.
    </p>

    <form method="POST" action="<?= url('excluir-conta') ?>" data-enter-submit>
      <?= getCsrfField() ?>

      <div class="form-group">
        <label for="senha">Confirme sua senha</label>
        <input type="password" id="senha" name="senha" required placeholder="Sua senha atual">
      </div>

      <div class="form-group">
        <label for="confirmacao">Digite <strong>EXCLUIR</strong> para confirmar</label>
        <input type="text" id="confirmacao" name="confirmacao" required
               placeholder="EXCLUIR" autocomplete="off" spellcheck="false">
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-danger btn-full"
                data-confirm="Excluir definitivamente sua conta e todas as suas avaliações?">
          🗑️ Excluir minha conta definitivamente
        </button>
        <a href="<?= url('painel') ?>" class="btn btn-ghost btn-full">Cancelar e voltar ao painel</a>
      </div>
    </form>
  </div>

  <p class="texto-pequeno">
    Prefere falar com uma pessoa? Escreva para
    <strong>privacidade@en430.appservice.tec.br</strong> — respondemos em até 15 dias.
  </p>
</div>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
