<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Atualização da Política de Privacidade - Sistema de Avaliação</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<div class="topbar">
  <h1>📄 Política de Privacidade atualizada</h1>
  <div class="topbar-user">
    <span>👤 <?= htmlspecialchars(getEstudanteNome()) ?></span>
    <a href="<?= url('logout') ?>" class="btn-sair">🚪 Sair</a>
  </div>
</div>

<div class="container container-narrow">
  <?= $flashes ?? '' ?>

  <div class="card">
    <h2>Precisamos do seu aceite para continuar</h2>
    <p>
      A Política de Privacidade e o Aviso de Tratamento de Dados foram atualizados.
      Enquanto o aceite da versão vigente não for registrado, o painel e as avaliações
      permanecem bloqueados — seu histórico está preservado e nada foi apagado.
    </p>

    <table class="tabela-simples">
      <thead>
        <tr><th>Versão aceita anteriormente</th><th>Versão vigente</th></tr>
      </thead>
      <tbody>
        <tr>
          <td><?= htmlspecialchars($versao_anterior ?? 'não registrada') ?></td>
          <td><strong><?= htmlspecialchars(CONSENTIMENTO_VERSAO) ?></strong></td>
        </tr>
      </tbody>
    </table>

    <p class="texto-pequeno">
      Leia o documento completo em
      <a href="<?= url('politica-de-privacidade') ?>" target="_blank" rel="noopener">Política de Privacidade</a>.
      O tratamento é o mesmo de antes: nome, e-mail, senha e, se informados, data de nascimento e
      telefone, usados para manter sua conta e registrar seu desempenho. A mudança de versão
      ajusta a redação e os canais de contato. Você pode excluir sua conta a qualquer momento em
      <a href="<?= url('excluir-conta') ?>">Excluir minha conta</a>.
    </p>

    <form method="POST" action="<?= url('reconsentimento') ?>">
      <?= getCsrfField() ?>

      <div class="form-group form-consent">
        <label class="consent-label" for="consentimento">
          <input type="checkbox" id="consentimento" name="consentimento" value="1" required>
          <span>
            Li e concordo com a versão <strong><?= htmlspecialchars(CONSENTIMENTO_VERSAO) ?></strong>
            da Política de Privacidade e do Aviso de Tratamento de Dados.
          </span>
        </label>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-full">✅ Aceitar e continuar</button>
      </div>
    </form>
  </div>

  <p class="texto-pequeno">
    Dúvidas sobre o tratamento dos seus dados? Escreva para
    <strong>privacidade@en430.appservice.tec.br</strong> — respondemos em até 15 dias.
  </p>
</div>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
