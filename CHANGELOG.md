# 📋 Changelog — Sistema de Avaliação EN_430

> **Disciplina:** Introdução à Enfermagem (EN_430)  
> **Repositório:** [github.com/hsoservicos/en430](https://github.com/hsoservicos/en430)

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/spec/v1.0.0.html).

---

## [Não lançado]

### Adicionado

- **Pesquisa da matéria 08 (Farmacologia e Cálculo de Medicamentos):** 7 entregáveis e banco de
  imagens com 4 ativos licenciados; 11 unidades em 70h, 300 itens especificados (114 de cálculo) e
  9 exemplares com gabarito conferido por cálculo próprio. Cinco unidades ficam marcadas como
  **bloqueadas** até fontes institucionais (lista de "certos", alta vigilância, diluição).
- **Fontes da matéria 07 conferidas:** Res. COFEN 736/2024 (cinco etapas do Processo de
  Enfermagem), Diretriz SBC de HAS 2020 (manguito por circunferência), guia EPUAP/NPIAP/PPPIA 2019
  e Protocolo ANVISA nº 3/2025 (ITU-AC), com links e data de consulta registrados.
- **RM-02 — Precisão, Didática e Veracidade:** regra de base imutável do conteúdo; nenhum
  material é elaborado sem fonte primária aberta, datada e conferida quanto à vigência
  (`docs/RM-02_PRECISAO_DIDATICA_E_VERACIDADE.md`).
- **RM-03 — Responsividade:** regra de base imutável do novo projeto; responsividade tratada
  como máxima garantia operacional, com matriz de 8 larguras e evidência obrigatória
  (`docs/RM-03_RESPONSIVIDADE.md`).
- **Pesquisa da matéria 07 (Fundamentos e Semiotécnica):** 6 entregáveis e banco de imagens com
  7 ativos licenciados; 400 itens especificados, 12 unidades em 120h, limite de escopo por unidade
  e checklists com itens eliminatórios. Fontes novas de semiotécnica ficam marcadas ⚪ até
  conferência; a deduplicação dos 2.803 registros está bloqueada por falta de acesso ao banco.
- **Mock de Design System e layout** em `/design-system/`: página estática de observação e
  validação com princípios, tokens (cor, tipografia, espaço, forma), componentes com estados,
  grade e pontos de quebra, acessibilidade verificável e tabela de registro de decisões; ligada ao
  rodapé do mock de apresentação (`mockups/design-system/index.html`).
- **RM-04 — Ilustração Obrigatória e Recursos Visuais:** regra de base imutável da imagem;
  toda matéria é elaborada com banco de imagens licenciadas, com legenda didática, `alt`, hash e
  lacunas registradas (`docs/RM-04_ILUSTRACAO_E_RECURSOS_VISUAIS.md`).
- **Bancos de imagens das matérias 01, 03, 04 e 05:** 23 imagens (22 de acervo licenciado e 1
  figura própria), com inventário, uso pedagógico por unidade, restrições de contexto e créditos
  (`pesquisas/<matéria>/imagens/`); catálogo em `pesquisas/RECURSOS_VISUAIS.md`.
- **Auditoria dos artefatos de pesquisa:** 21 fichas verificadas, 7 achados e 1 correção
  (`docs/auditorias/AUDITORIA_ARTEFATOS_PESQUISA_2026-11-23.md`).
- **Pesquisa da matéria 03 (Biossegurança/CCIH):** 7 entregáveis e 20 fontes com status de
  vigência (RDC 222/2018, RDC 15/2012, RDC 36/2013, NR-32, Portaria 2.616/1998).
- **Pesquisa da matéria 04 (Políticas Públicas de Saúde e SUS):** 6 entregáveis, 250 itens
  especificados e 24 fontes com vigência; corrige o erro de material antigo que ainda trata o
  Previne Brasil como modelo vigente (substituído pela Portaria 3.493/2024).
- **Política de Privacidade do novo domínio** em `/politica-de-privacidade/`, com escopo do site
  de apresentação; o link do rodapé do mock deixou de responder 404.
- **Roadmap único** em `PROGRESSO_PLANEJAMENTO.md` §6: 44 semanas, MVP de 4 componentes; PRD e
  plano de expansão passam a referenciá-lo (achado A3).
- **Pesquisa da matéria 05 (Ética, Bioética e Legislação):** 6 entregáveis, 200 itens
  especificados e 22 fontes; registra que o CEPE 564/2017 segue vigente e que a minuta do novo
  Código foi aprovada em 19/03/2026, com vacatio de 120 dias após o DOU.
- **Pacotes de revisão das matérias 03 e 04:** envelopes com artefatos, ordem de leitura,
  perguntas para enfermeiro e educador e critério de parecer (7 arquivos cada; 782 e 844 linhas).

### Infraestrutura

- **EN_430 estacionado:** auto-deploy desabilitado no app `2iweyqxekxmminhiwgyv5bvk`
  (`is_auto_deploy_enabled=false`) em 2026-11-23; o app permanece `running:healthy` e
  `https://en430.appservice.tec.br/` responde 200. Publicação passa a ser manual e consciente.
- **Mock do novo projeto publicado** em <https://enfermagem.appservice.tec.br/>: projeto Coolify
  `enfermagem` (`d7ldx6uqgsvdeal3u0kjk3lq`) e app `enfermagem-web` (`ey4wpwwencsnvj9xluoxnyjj`),
  build `static` a partir de `/mockups` na branch `mock/landing-enfermagem`. Isolado do projeto
  `en430`, que continua em sua URL e sem deploy cruzado.

### Corrigido

- **Build da imagem Docker** voltou a funcionar: o `.dockerignore` excluía `docs/` e o `COPY` da
  política quebrava o build; adicionada exceção para o arquivo canônico.
- **`/scripts/*.php` bloqueado por HTTP** no `.htaccess` (antes a rotina de inativas respondia 200
  com o shebang impresso).

### Segurança

- **HSTS e cookie `Secure`:** o `.htaccess` passa a enviar HSTS e `requisicaoSegura()` lê
  `CF-Visitor`/`X-Forwarded-Proto` para marcar o cookie de sessão como `Secure`. O redirect HTTP→HTTPS
  fica na Cloudflare (*Always Use HTTPS*), pois o Apache recebe HTTP do túnel e redirecionar na origem
  criava loop.
- **Verificação do stack** contra os CVEs de novembro/2026: Apache 2.4.71 já contém as correções e
  a linha PHP foi migrada de 8.4.28 para **8.6.0**; relatório em
  `docs/auditorias/RELATORIO_SEGURANCA_STACK_2026-11-22.md`.
- **Verificação de produção e redeploy:** Coolify saudável; redeploy forçado publicou `0cf5ed2` e a
  produção passou a responder em **PHP 8.6.0**, com `/politica-de-privacidade` em 200; Cloudflare Tunnel
  com 4 conexões HA. O *Always Use HTTPS* do painel Cloudflare segue pendente (sem token de API) e o
  `.htaccess` já cobre HSTS/redirect na origem (seção 8.5 de `docs/ESTADO_ATUAL.md`).
- **Hardening do runtime:** `expose_php=Off`, `display_errors=Off` (log em stderr), sessão com
  `HttpOnly` e `use_strict_mode`, `session.upload_progress=0`, `ServerTokens Prod` e bloqueio
  HTTP de `.xml`, `.ini`, `.sh`, `.sql` e afins.
- **Varredura de imagem com Trivy** em `.github/workflows/trivy.yml`: build + scan a cada
  push/PR na `master` e semanalmente, falhando em HIGH/CRITICAL corrigíveis.

### Alterado

- **Migração para PHP 8.6** (`php:8.6-apache`, suporte de segurança até 31/12/2030): a linha 8.4
  perde o suporte em 31/12/2026. A imagem passou a compilar `mbstring` (ausente na base 8.6) e o
  `composer.json`/`composer.lock` exigem `php >=8.6`. Suíte validada na nova versão: 180 testes,
  461 asserções, 0 falhas.
- **`curl_close()` removido** de `tests/IntegrationTest.php` (sem efeito desde o PHP 8.0 e
  depreciado na 8.5+); o identificador é liberado com `unset()`.
- **Metadados dos testes em attributes do PHPUnit 11**: `#[RunInSeparateProcess]` e
  `#[PreserveGlobalState(false)]` substituem as anotações em doc-comment em
  `tests/FrontControllerTest.php`, eliminando as 210 depreciações do runner.

### Adicionado

### 🔬 Frente de pesquisas por matéria

- Criada a estrutura completa de `pesquisas/`: 4 módulos, 17 fichas de matéria, 4 fichas de
  campo de estágio, com `fontes/` e `artefatos/` por matéria.
- Concluída a pesquisa da **matéria 01 — Anatomia e Fisiologia Humana**: ficha, mapa de
  12 unidades, matriz de competências, 300 itens especificados, 6 materiais e 18 fontes
  registradas com licença.
- Produzida a **pré-revisão técnica e pedagógica** (16 achados: 5 altos, 7 médios, 4 baixos),
  com correções aplicadas e roteiro de pendências para validação por enfermeiro e educador.
- Adicionado guia de colaboração para professores, revisores e gestão educacional.

### ⚖️ Jurídico e conformidade

- Adicionado o **Termo de Confidencialidade e Sigilo (v1.0)** em `docs/juridico/`: modelo
  preenchível por colaborador, com sigilo, proteção de dados (LGPD), cessão de direitos autorais,
  segurança da informação, sanções e anexos de escopo e de devolução.
- Criado o **registro de emissão** dos termos por colaborador, com estados da RM-01.
- Adicionado o **Termo de Adesão do Professor Validador (v1.0):** revisão técnica e pedagógica,
  parecer com achados classificados, prazos de resposta, impedimentos e responsabilidade.
- Fixados os parâmetros em aberto: multa de R$ 5.000,00 (dobro em caso agravado), canais
  `juridico@` e `seguranca@en430.appservice.tec.br`.

### 🔒 Privacidade e proteção de dados

- Adicionada a **Política de Privacidade (v1.0)** para os estudantes do sistema de avaliação, com
  os dados efetivamente tratados pelo código, bases legais, retenção, segurança e direitos do
  titular (LGPD).
- Adicionado o **Aviso de Tratamento de Dados (v1.0)** para a tela de cadastro, com texto pronto,
  rótulo do checkbox de consentimento e pendências de implementação.
- Criado o **pacote de validação da matéria 01** (`pesquisas/.../01-.../validacao/`) para envio ao
  primeiro professor validador, com prazo de parecer definido.
- O cadastro passou a exigir **checkbox de consentimento**, gravando data/hora e versão do aceite
  (`consentimento_em`, `consentimento_versao`).
- Publicada a política em **`/politica-de-privacidade`**, renderizada do documento canônico.
- O estudante pode **excluir a própria conta** em `/excluir-conta`, com senha, confirmação textual
  e registro de auditoria; bancos antigos recebem as colunas automaticamente.
- Implantado o **reconsentimento**: quando a versão da política muda, painel e avaliações ficam
  bloqueados até o aceite em `/reconsentimento`, registrado com data, versão e auditoria.
- Criada a **retenção de contas inativas**: sem acesso há 24 meses, a conta recebe aviso por e-mail
  e, após 30 dias sem retorno, é excluída com as avaliações; execução por
  `scripts/rotina_inativas.php` e acompanhamento no painel administrativo.

### ⚖️ Governança do progresso

- Criada a **RM-01 — Regra Máxima de Gestão, Controle e Auditoria do Progresso**
  (`docs/GOVERNANCA_PROGRESSO.md`), com precedência sobre os demais documentos em matéria de
  método: evidência obrigatória, fonte de verdade única por domínio, atualização no mesmo commit,
  vocabulário fechado de status, histórico append-only, regras de contagem e auditoria mensal.
- Regra vinculada em `README.md`, `docs/README.md`, `docs/ESTADO_ATUAL.md`,
  `pesquisas/README.md` e `pesquisas/PROGRESSO.md`.
- Criada a **fonte de verdade do planejamento**
  (`docs/planejamento/PROGRESSO_PLANEJAMENTO.md`): 6 gates, Q1–Q8, plano de ação de 15 dias e
  checklist do G0-C, com histórico append-only — fecha o achado A1 da auditoria.
- **RM-01 v1.1:** emenda que inclui o domínio "planejamento" na tabela de fontes de verdade (§3)
  e na aplicação imediata (§15).
- **Plano de expansão v1.1:** correção factual do estado para PHP 8.6, 180 testes e 469 asserções,
  com vínculo ao controle de progresso e bloco de controle do documento (achado A2).

### 📚 Planejamento da expansão do curso

- Adicionada a **ementa de referência** do curso Técnico em Enfermagem (`docs/ementa.md`):
  1.200h teórico-práticas + 600h de estágio, 4 módulos e 17 componentes.
- Criado o **plano de expansão** (`docs/planejamento/EXPANSAO_CURSO_TECNICO_ENFERMAGEM.md`)
  com diagnóstico, arquitetura-alvo, matriz de cobertura, roadmap, esforço, KPIs e riscos.
- Plano reconciliado com a ementa: 17 componentes, 600h de estágio, meta de 4.400 itens
  únicos e priorização dos componentes da fase 2.
- Criado o **PRD do programa de apoio** (`docs/planejamento/PRD_APOIO_FORMACAO_TECNICA_ENFERMAGEM.md`)
  com limites legais, personas, requisitos funcionais/não funcionais, dependências, roadmap,
  riscos e critérios de aceite. Verificação normativa: CNCT fixa 1.200h, mas **não** fixa
  600h de estágio em nível federal; o PRD adota 600h como referência parametrizável por PPC.

---

## [2.2.0] — 2026-11-21

### 🗂️ Organização e profissionalização da estrutura

- Reorganização do repositório em `docs/`, `materiais/` e `scripts/`, com links relativos atualizados.
- Documentação de deploy migrada para `docs/deploy/` e novo guia oficial do Coolify.
- Padrões de projeto: `.editorconfig`, `.gitattributes`, `.dockerignore` e verificador de links
  (`scripts/verificar_links.py`).
- Criação de [`docs/ESTADO_ATUAL.md`](docs/ESTADO_ATUAL.md) como fonte de verdade do projeto.
- Correção do `.gitignore` (comentários inline invalidavam padrões) e remoção de 1.738 arquivos
  gerados do versionamento (`vendor/`, `reports/`, `coverage-detail/`, caches).

### 🐳 Build e deploy

- `Dockerfile` movido para a raiz, com contexto do repositório e cópia dos materiais para `/materiais/`.
- Base directory do Coolify alterada de `/sistema_avaliacao/php` para `/`.
- `APP_DB_PATH` corrigido em `config.php`, `migrate.php` e `backup.sh` para o volume persistente `/data`.
- Deploy documentado, publicado e validado em <https://en430.appservice.tec.br>.

### 🎓 Materiais e navegação

- Central de Estudos servida em `/materiais/html/index_estudos.html` (HTML + PDFs).
- Botão **📚 Materiais de Estudo** na home e no login do sistema.
- Botão **📝 Ir para o Sistema de Avaliação** na Central de Estudos.
- Botão **← Voltar ao início** no login.

### 🔢 Números conferidos na fonte

- **2.803 questões** (1.378 Fácil / 739 Médio / 686 Difícil) em 10 módulos.
- **158 casos de teste** (385 asserções documentadas).

### 🧪 Base de apoio

- `aprendizado_em_ia/` isolado como repositório independente.
- Projeto e aplicação `aprendizado-em-ia` removidos do Coolify; a base não é publicada.

---

## [2.1.0] — 2026-07-17

### 🚀 Finalização e Publicação

Aprimoramentos finais para publicação do sistema com módulo administrativo completo, balanceamento de questões e documentação de deploy.

#### Adicionado

##### ⚙️ Módulo Administrativo
- [x] CRUD completo de estudantes (listar, filtrar, excluir, resetar senha)
- [x] Paginação na lista de estudantes (15 por página)
- [x] Filtro de busca por nome, email ou telefone
- [x] Exportação CSV de estudantes, avaliações e questões
- [x] Log de auditoria com últimas 50 ações administrativas
- [x] Estatísticas globais no painel admin (estudantes, questões, avaliações)
- [x] Distribuição de questões por módulo e dificuldade
- [x] Visualização das últimas questões adicionadas

##### 🔧 Core do Sistema
- [x] router.php para servidor PHP built-in (php -S)
- [x] Rate limiting de login (5 tentativas/minuto)
- [x] Secure flag dinâmico no cookie de sessão

##### 🗄️ Banco de Dados
- [x] Balanceamento de questões para ~250 por módulo
- [x] **2.803 questões** (aumento de 328 questões)
- [x] Tabela `admin_log` para auditoria de ações

##### 🧪 Testes
- [x] Expansão para **158 testes e 385 asserções**
- [x] FrontControllerDatabaseTest com testes de integração
- [x] Cobertura: **91.15%** functions.php, **63.06%** index.php, **~54%** geral

##### 📖 Documentação
- [x] GUIA_PUBLICACAO_XAMPP_WINDOWS10.md — Guia completo para XAMPP (20 seções)
- [x] README.md atualizado com números corretos (2.803 questões, 158 testes)

#### 🔄 Modificado

| Item | Antes | Depois |
|:-----|:------|:--------|
| **Questões** | 2.475 | 2.803 (balanceado) |
| **Testes** | 141 (350 asserções) | 158 (385 asserções) |
| **Coverage functions.php** | 86.59% | 91.15% |
| **Coverage index.php** | 16.67% | 63.06% |
| **Admin** | Básico (login + stats) | CRUD completo + CSV + Log |

---

## [2.0.0] — 2026-07-16

### 🎉 Reformulação Completa

Migração completa do sistema de Python/Flask para **PHP 8 + Apache + SQLite**.

---

## [1.0.0] — 2026-07 (Original)

### ✨ Versão Original (Python/Flask)

- [x] Sistema básico em Flask + Jinja2 + SQLite
- [x] Cadastro e login de estudantes
- [x] Banco de questões com módulos de 1 a 10
- [x] Avaliação com 20 questões aleatórias
- [x] Resultado com pontuação básica
- [x] Templates HTML com CSS básico
- [ ] 🔴 Sem testes automatizados
- [ ] 🔴 Sem proteção CSRF
- [ ] 🔴 Senhas sem hash (texto plano)
- [ ] 🔴 Sem documentação
- [ ] 🔴 Sem scripts de deploy

---

<div align="center">

*Changelog mantido a partir de Julho de 2026*  
*Sistema de Avaliação — Introdução à Enfermagem (EN_430)*  
*PHP 8 + Apache + SQLite*

</div>
