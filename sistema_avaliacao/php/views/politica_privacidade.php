<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="index, follow">
<title>Política de Privacidade - Sistema de Avaliação</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body class="doc-body">
<div class="doc-topbar">
  <a href="<?= url('') ?>" class="doc-brand">📝 Sistema de Avaliação — EN_430</a>
  <a href="<?= estaLogado() ? url('painel') : url('login') ?>" class="doc-back">
    <?= estaLogado() ? '← Voltar ao painel' : '← Ir para o login' ?>
  </a>
</div>

<main class="doc-page">
  <?= $flashes ?? '' ?>
  <?= $conteudo ?? '' ?>

  <footer class="doc-footer">
    <p>
      Versão <strong><?= htmlspecialchars($versao ?? CONSENTIMENTO_VERSAO) ?></strong> ·
      Publicada em 21/11/2026 ·
      Dúvidas e pedidos de titular: <strong>privacidade@en430.appservice.tec.br</strong>
    </p>
    <p>
      <a href="<?= url('cadastro') ?>">Criar conta</a> ·
      <a href="<?= url('') ?>">Página inicial</a>
    </p>
  </footer>
</main>
</body>
</html>
