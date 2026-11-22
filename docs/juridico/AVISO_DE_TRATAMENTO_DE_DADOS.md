# AVISO DE TRATAMENTO DE DADOS PESSOAIS

**Sistema de Avaliação — Projeto EN_430** · versão 1.0 · vigência 21/11/2026

> **Este aviso é o resumo.** O documento completo é a
> [Política de Privacidade](POLITICA_DE_PRIVACIDADE.md), que prevalece em caso de dúvida.

---

## 1. Quem trata seus dados

O controlador é **Humberto Santos de Oliveira** (CPF 715.591.404-53), mantenedor do Projeto EN_430.
Contato para assuntos de privacidade: **privacidade@en430.appservice.tec.br**.

---

## 2. Quais dados, para quê e com qual base legal

| Dado | Para quê | Base legal |
|---|---|---|
| **Nome**, **e-mail** e **senha** (hash bcrypt) | Criar sua conta, autenticar e identificar seus resultados | Consentimento (art. 7º, I) |
| **Data de nascimento** e **telefone** *(opcionais)* | Adequação etária e recuperação de acesso | Consentimento (art. 7º, I) |
| **Respostas, pontuação e datas das avaliações** | Corrigir, mostrar seu desempenho e melhorar o banco de itens | Consentimento (art. 7º, I) |
| **Cookie de sessão**, hash de IP e logs de acesso | Manter você conectado, evitar fraude e cumprir o art. 15 do Marco Civil | Legítimo interesse (art. 7º, IX) e obrigação legal (art. 7º, II) |

**Não coletamos** dados de saúde, diagnóstico ou prontuário. **Não usamos** seus dados para
propaganda, venda ou perfilamento comercial.

---

## 3. Com quem compartilhamos

Apenas com o **provedor de hospedagem** que executa o sistema, com autoridades quando houver ordem
legal e com profissionais de segurança/jurídico em caso de incidente. **Nunca vendemos dados.**

---

## 4. Por quanto tempo guardamos

- Conta e avaliações: **enquanto a conta existir**.
- Após pedido de exclusão: eliminados em até **30 dias**.
- Logs de acesso: **6 meses** (Marco Civil). Registros de auditoria: **5 anos**.

---

## 5. Seus direitos

Você pode pedir **confirmação, acesso, correção, portabilidade, anonimização, eliminação,
informação sobre compartilhamento e revogação do consentimento**, além de se opor a tratamento por
legítimo interesse e reclamar à **ANPD**.

**Como:** escreva para **privacidade@en430.appservice.tec.br**, com nome e e-mail cadastrados.
Respondemos em até **15 dias**. A exclusão é **irreversível** e remove conta e avaliações.

---

## 6. Cookies

Usamos **um cookie essencial** de sessão (24h, `HttpOnly`, `SameSite=Lax`) para manter o login.
Sem ele, o acesso não funciona. **Não há** cookies de publicidade ou rastreamento.

---

## 7. Segurança

Senhas em hash bcrypt, HTTPS, proteção CSRF, limite de tentativas de login e registro de ações
administrativas. Em caso de incidente relevante, os titulares afetados serão comunicados, nos termos
do art. 48 da LGPD.

---

## 8. Menores de idade

O sistema é destinado sobretudo a maiores de 18 anos. Menor de 16 anos só deve se cadastrar com
consentimento e acompanhamento de responsável legal (art. 14 da LGPD). O responsável pode pedir
acesso, correção ou eliminação pelo canal do item 5.

---

## 9. Texto para a tela de cadastro (copiar)

> Ao criar sua conta, você concorda com o tratamento dos seus dados pessoais (nome, e-mail, senha e,
> se informados, data de nascimento e telefone) para criar e manter sua conta, corrigir avaliações e
> exibir seu desempenho, nos termos da **LGPD** e da
> [Política de Privacidade](https://en430.appservice.tec.br/politica-de-privacidade).
> Você pode acessar, corrigir ou excluir seus dados a qualquer momento em **Excluir minha conta**
> (no painel) ou pelo e-mail **privacidade@en430.appservice.tec.br**.

**Rótulo do checkbox:**

> [ ] Li e concordo com o **Aviso de Tratamento de Dados** e com a **Política de Privacidade**, e
> autorizo o tratamento dos meus dados para as finalidades informadas.

---

## 10. Pendências de implementação

> Registro interno, para acompanhamento conforme a [RM-01](../GOVERNANCA_PROGRESSO.md).

- [x] Incluir no formulário de cadastro (`sistema_avaliacao/php/views/cadastro.php`) o **checkbox de
      aceite** obrigatório, com link para a política (`/politica-de-privacidade`).
- [x] Publicar a política em rota pública (`/politica-de-privacidade`), renderizada do documento
      canônico `docs/juridico/POLITICA_DE_PRIVACIDADE.md`.
- [x] Registrar no banco a **data e a versão do aceite** (`consentimento_em`, `consentimento_versao`).
- [x] Implantar rotina de eliminação de conta pelo próprio titular (`/excluir-conta`), com auditoria.
- [ ] Criar o alias `privacidade@en430.appservice.tec.br` — o canal ainda não existe; até lá, os
      pedidos devem ser encaminhados ao mantenedor.
- [ ] Definir a rotina de contas inativas (24 meses) no painel administrativo.
- [ ] Coletar novo aceite quando a política mudar de versão (mecanismo de reconsentimento).

---

*Aviso de Tratamento de Dados v1.0 · Projeto EN_430 · 21/11/2026 · sujeito a revisão jurídica.*
