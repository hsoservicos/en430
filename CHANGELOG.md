# 📋 Changelog — Sistema de Avaliação EN_430

> **Disciplina:** Introdução à Enfermagem (EN_430)  
> **Repositório:** [github.com/hsoservicos/en430](https://github.com/hsoservicos/en430)

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/spec/v1.0.0.html).

---

## [Não lançado]

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

### ⚖️ Governança do progresso

- Criada a **RM-01 — Regra Máxima de Gestão, Controle e Auditoria do Progresso**
  (`docs/GOVERNANCA_PROGRESSO.md`), com precedência sobre os demais documentos em matéria de
  método: evidência obrigatória, fonte de verdade única por domínio, atualização no mesmo commit,
  vocabulário fechado de status, histórico append-only, regras de contagem e auditoria mensal.
- Regra vinculada em `README.md`, `docs/README.md`, `docs/ESTADO_ATUAL.md`,
  `pesquisas/README.md` e `pesquisas/PROGRESSO.md`.

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
