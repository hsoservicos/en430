# ⚖️ Jurídico e conformidade

Instrumentos jurídicos e declarações que condicionam a participação de colaboradores no projeto.

| Documento | Conteúdo |
|-----------|----------|
| [**TERMO_CONFIDENCIALIDADE_SIGILO.md**](TERMO_CONFIDENCIALIDADE_SIGILO.md) | **Termo de Confidencialidade e Sigilo (v1.0):** modelo preenchível por colaborador — sigilo, LGPD, cessão de direitos, sanções e Anexos I e II |
| [**TERMO_ADESAO_PROFESSOR_VALIDADOR.md**](TERMO_ADESAO_PROFESSOR_VALIDADOR.md) | **Termo de Adesão do Professor Validador (v1.0):** revisão técnica e pedagógica, parecer, prazos e responsabilidade — complementar ao TCS |
| [**POLITICA_DE_PRIVACIDADE.md**](POLITICA_DE_PRIVACIDADE.md) | **Política de Privacidade (v1.0):** dados tratados pelo sistema de avaliação, bases legais, retenção, segurança e direitos do titular |
| [**AVISO_DE_TRATAMENTO_DE_DADOS.md**](AVISO_DE_TRATAMENTO_DE_DADOS.md) | **Aviso de Tratamento de Dados (v1.0):** resumo para a tela de cadastro, texto pronto e pendências de implementação |

---

## Ordem de assinatura

1. **TCS** — obrigatório para todo colaborador que acesse conteúdo não publicado, código, dados de
   estudantes ou informação comercial.
2. **Termo de Adesão do Professor Validador** — apenas para quem emite parecer de validação; é
   **complementar** ao TCS e a ele remete (cláusula 4).
3. **Anexos** preenchidos: escopo no TCS (Anexo I) e designação da matéria no termo de adesão
   (Anexo I).

---

## Como emitir um termo

0. **Candidatura:** o interessado se apresenta pelo formulário de colaboradores, que registra os
   dados de triagem sem CPF/RG. O fluxo completo — campos, estados e geração automática do termo —
   está no [planejamento do formulário de colaboradores](../planejamento/PLANEJAMENTO_FORMULARIO_COLABORADORES.md).
1. Copie o modelo e preencha **somente** os campos entre colchetes (`[____]`).
2. Preencha o **Anexo I** — é ele que delimita o que a pessoa pode conhecer e fazer.
3. Colete assinaturas (duas vias ou assinatura eletrônica) e as duas testemunhas.
4. Registre a emissão na tabela abaixo e arquive o exemplar assinado em local **privado**.
5. Ao término, revogue acessos e colha o **Anexo II** do TCS (devolução e eliminação).

> **O exemplar assinado não é versionado neste repositório.** O repositório é público e os termos
> contêm CPF, RG e endereço — dados pessoais que, por força da LGPD (art. 46), exigem controle de
> acesso. Versionam-se apenas os **modelos** e este **registro** (sem dados sensíveis).

---

## Registro de emissão

| Nº | Colaborador(a) | Termo | Função / frente | Versão | Emissão | Situação |
|:--:|----------------|:-----:|-----------------|:------:|---------|----------|
| — | *(nenhum termo emitido)* | — | — | — | — | — |

**Situação:** ⬜ não emitido · 🟨 emitido, aguardando assinatura · 🟦 assinado e ativo ·
🟪 encerrado, aguardando Anexo II · ✅ encerrado com Anexo II e acessos revogados.

Estados conforme a [RM-01](../GOVERNANCA_PROGRESSO.md). Cada linha registra o número do termo, que
deve ser anotado no exemplar assinado.

---

## Parâmetros fixados pelo outorgante

| Parâmetro | Valor vigente | Onde consta |
|-----------|---------------|-------------|
| Multa compensatória por infração | **R$ 5.000,00** | TCS, cláusula 14.1, "a" |
| Multa agravada (dado sensível, divulgação pública ou uso comercial) | **R$ 10.000,00** | TCS, cláusula 14.1, "a" |
| Correção monetária | IPCA desde a assinatura | TCS, cláusula 14.1, "a" |
| Notificações contratuais | juridico@en430.appservice.tec.br | TCS, itens 1.1 e 15.1 |
| Incidentes de segurança | seguranca@en430.appservice.tec.br (cópia para juridico@) | TCS, cláusula 15.2 |
| Prazo de comunicação de incidente | 24 horas | TCS, cláusula 7.5 |
| Privacidade e direitos do titular | privacidade@en430.appservice.tec.br | Política, item 11 |
| Publicação da política | `/politica-de-privacidade` (renderizada do documento canônico) | Política, cabeçalho |
| Versão do consentimento registrada no cadastro | `CONSENTIMENTO_VERSAO` = 1.0 | `sistema_avaliacao/php/config.php` |
| Prazo de resposta ao titular | 15 dias | Política, item 10.2 |
| Prazo do parecer de validação | 15 dias corridos | Adesão, cláusula 9 |
| Alerta de risco grave ao paciente | 24 horas | Adesão, cláusula 9 |
| Não aliciamento | 12 meses | TCS, cláusula 11.2 |
| Foro | Comarca de Paulista/PE | TCS, cláusula 20.2 |

---

## Pendências

- [ ] **Criar os aliases de e-mail** `juridico@`, `seguranca@` e `privacidade@` no domínio
      `en430.appservice.tec.br` — os prazos das cláusulas 7.5 e 15.2 e o canal do titular pressupõem
      caixa ativa.
- [x] Publicar a política em rota pública do sistema (`/politica-de-privacidade`).
- [x] Adicionar o **checkbox de aceite** e registrar data e versão no cadastro.
- [x] Implantar a exclusão de conta pelo próprio titular (`/excluir-conta`).
- [x] Coletar novo aceite quando a política mudar de versão: `/reconsentimento` bloqueia painel e
      avaliações até o aceite da versão vigente, com registro e auditoria.
- [ ] Revisão dos dois modelos por advogado(a) inscrito(a) na OAB antes da primeira assinatura.
- [ ] Definir os valores de remuneração, quando houver colaboração remunerada (Anexo I).
- [ ] Designar o primeiro(a) professor(a) validador(a) e emitir os dois termos na mesma ocasião.

---

*Criado em 21/11/2026 · v1.1 · sujeito à [RM-01](../GOVERNANCA_PROGRESSO.md).*
