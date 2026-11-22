# 📍 Estado Atual do Projeto — EN_430

**Atualizado em:** 2026-11-21
**Versão:** 2.2.0
**Branch de produção:** `master`

Este documento é a fonte de verdade sobre a condição atual do repositório.
Substitui métricas e procedimentos de documentos históricos (auditorias e guias antigos).

> **Governança:** a medição, o registro e a auditoria de todo progresso seguem a regra máxima
> [**RM-01 — Governança de Progresso**](GOVERNANCA_PROGRESSO.md), que prevalece sobre este e
> qualquer outro documento em matéria de método. Nenhum número aqui vale sem fonte e data.

---

## 1. Produto publicado

| Item | Valor |
|------|-------|
| **URL** | <https://en430.appservice.tec.br> |
| **Plataforma** | Docker + Coolify (projeto `en430`) |
| **Build** | `Dockerfile` na raiz, contexto = repositório completo |
| **Porta** | 80 (Apache + PHP 8.4) |
| **Banco** | SQLite em volume persistente `/data/avaliacao.db` |
| **Health check** | `GET /` → 200 |
| **Status** | `running:healthy` |
| **Deploy** | automático a cada push na `master` |

**Rotas principais:**

| Rota | Descrição |
|------|-----------|
| `/` | Home do sistema de avaliação |
| `/login` · `/cadastro` | Acesso do estudante |
| `/painel` | Painel do estudante autenticado |
| `/materiais/html/index_estudos.html` | Central de Estudos (HTML + PDFs) |

**Navegação entre estudo e avaliação:**

- Home do sistema → botão **📚 Materiais de Estudo**.
- Central de Estudos → botão **📝 Ir para o Sistema de Avaliação**.
- Login → botão **← Voltar ao início** e link **📚 Materiais de estudo**.

---

## 2. Base de apoio (não publicada)

`aprendizado_em_ia/` é um **repositório Git independente**
(`hsoservicos/aprendizado_em_ia`) usado como base de estudos e apoio ao
desenvolvimento de novos projetos.

- **Não é publicado no Coolify.**
- O projeto e a aplicação `aprendizado-em-ia` foram **removidos da instância Coolify**.
- Não participa do build nem do runtime do sistema de avaliação.
- Está ignorado pelo Git do `en430` (`.gitignore`).

---

## 3. Estrutura do repositório

```text
en430/
├── index.html                  # Portal dos materiais
├── README.md                   # Visão geral + início rápido
├── CHANGELOG.md                # Histórico de versões
├── Dockerfile                  # Imagem de produção (contexto = raiz)
├── .dockerignore / .editorconfig / .gitattributes / .gitignore
│
├── docs/
│   ├── ESTADO_ATUAL.md         # Este documento
│   ├── README.md               # Índice da documentação e padrões
│   ├── ementa.md               # Matriz curricular do curso técnico
│   ├── planejamento/           # Plano de expansão e PRD do programa de apoio
│   ├── deploy/                 # COOLIFY.md (oficial) + guias legados Apache/XAMPP
│   ├── auditorias/             # Auditoria e relatório de evolução (históricos)
│   ├── referencias/            # Ementa e anotações da disciplina
│   └── arquivo/                # Artefatos históricos
│
├── materiais/
│   ├── html/                   # 12 materiais interativos
│   └── pdf/                    # 16 PDFs
│
├── scripts/                    # Instaladores + verificar_links.py
├── sistema_avaliacao/          # Aplicação PHP + documentação técnica
└── aprendizado_em_ia/          # Repo independente (base de apoio, ignorado)
```

---

## 4. Números atuais

### 4.1 Banco de questões — 2.803

Fonte: `sistema_avaliacao/php/scripts/questions_data.php` (495 questões-base)
expandido por `recriar_questoes.php` (ciclos de 5 a 7 por módulo).

| Módulo | Tema | Fácil | Médio | Difícil | Total |
|:------:|:-----|:----:|:-----:|:-------:|:-----:|
| 1 | Fundamentos — Teoria das Necessidades Básicas | 240 | 120 | 150 | **510** |
| 2 | Processo de Enfermagem (SAE) | 150 | 50 | 50 | **250** |
| 3 | Terminologias e Sinais Vitais | 145 | 55 | 50 | **250** |
| 4 | Administração de Medicamentos — Vias Enterais | 138 | 66 | 54 | **258** |
| 5 | Administração de Medicamentos — Vias Parenterais | 126 | 78 | 54 | **258** |
| 6 | Assepsia, Antissepsia e Higiene do Paciente | 126 | 78 | 60 | **264** |
| 7 | Curativos, Feridas e Coberturas | 132 | 78 | 60 | **270** |
| 8 | Oxigenoterapia e Cateterismos | 108 | 66 | 66 | **240** |
| 9 | Exames, Coletas, Medidas, Crioterapia e Termoterapia | 108 | 78 | 72 | **258** |
| 10 | Admissão, Alta, Contenção e Processo Pós-Morte | 105 | 70 | 70 | **245** |
| | **Total** | **1.378** | **739** | **686** | **2.803** |

### 4.2 Testes

| Item | Valor |
|------|-------|
| Casos de teste | **158** |
| Asserções documentadas | **385** |
| Framework | PHPUnit 11.x |
| Arquivos | `AuthTest`, `AvaliacaoTest`, `DatabaseTest`, `FunctionsUtilTest`, `FrontControllerTest`, `FrontControllerDatabaseTest`, `IntegrationTest` |

### 4.3 Outros

| Item | Valor |
|------|-------|
| Questões por avaliação | 20 (aleatórias, sem repetição) |
| Módulos | 10 |
| Materiais HTML / PDF | 12 / 16 |
| Links locais verificados | 676 (0 quebrados em 2026-11-21) |

---

## 5. O que mudou nesta sessão

- Reorganização do repositório em `docs/`, `materiais/` e `scripts/`, com links atualizados.
- Auditoria de referências cruzadas e criação do verificador `scripts/verificar_links.py`.
- Padrões de projeto: `.editorconfig`, `.gitattributes`, `.dockerignore` e `.gitignore` corrigido
  (padrões com comentário inline não funcionavam).
- Remoção de 1.738 arquivos gerados do versionamento (`vendor/`, `reports/`, `coverage-detail/`, cache).
- Dockerfile movido para a raiz, com contexto do repositório e cópia dos materiais para `/materiais/`.
- Integração dos materiais ao sistema com botões de navegação e retorno no login.
- Correção do `APP_DB_PATH` em `config.php`, `migrate.php` e `backup.sh` para o volume `/data`.
- Documentação do deploy real no Coolify (`docs/deploy/COOLIFY.md`).
- Remoção do projeto/app `aprendizado-em-ia` da instância Coolify.
- Novo build, publicação e validação dos endpoints em produção.

---

## 6. Verificações realizadas

| Verificação | Resultado |
|-------------|-----------|
| `scripts/verificar_links.py` | 0 links quebrados |
| `docker build` (contexto raiz) | sucesso |
| Container com volume `/data` | banco criado e servido |
| Home, login, materiais e PDF | HTTP 200 |
| Deploy Coolify | `finished` / `running:healthy` |
| App `aprendizado-em-ia` no Coolify | removido |

> A suíte PHPUnit não foi executada nesta sessão porque o host não possui PHP
> instalado; a sintaxe foi validada com `php -l` em container e o build/teste
> funcional da aplicação foi concluído. O número de casos (158) é a contagem
> dos métodos de teste versionados.

---

## 7. Publicação da frente de conteúdo e governança (2026-11-21)

Sincronização no GitHub para acesso por professores, revisores e gestão educacional.

| Commit | Conteúdo |
|--------|----------|
| `3886720` | Correção da base normativa do CNCT (CNE/CEB nº 2/2020) e da natureza da carga de estágio |
| `018887e` | Frente de pesquisas: 4 módulos, 17 fichas de matéria, 4 campos de estágio e os 7 entregáveis da matéria 01 (🟪 em revisão) |
| `9783094` | RM-01 — regra máxima de gestão, controle e auditoria do progresso |

**Novos artefatos publicados:**

- `pesquisas/` — 76 arquivos (fichas, registros de fontes e artefatos).
- `docs/GOVERNANCA_PROGRESSO.md` — regra RM-01, vinculada a este documento, ao `README.md`,
  ao `docs/README.md` e ao `pesquisas/PROGRESSO.md`.
- Guia de colaboração para validação em `pesquisas/README.md`, seção 6.
- `docs/juridico/` — Termo de Confidencialidade e Sigilo, Termo de Adesão do Professor Validador,
  Política de Privacidade e Aviso de Tratamento de Dados (todos v1.0), com registro de emissão e
  parâmetros fixados (multa, canais, prazos).
- `pesquisas/.../01-.../validacao/` — pacote de envio da matéria 01 ao primeiro professor
  validador; envio pendente de designação, prazo do parecer 2026-12-06.

| Verificação | Resultado |
|-------------|-----------|
| `scripts/verificar_links.py` | 845 arquivos, 785 links locais, 0 quebrados |
| `git status` | árvore limpa após os commits |
| `git push origin master` | `607b6a1..9783094` aceito |

> **Estado das matérias:** 0 concluídas, 1 em revisão (matéria 01), 16 com escopo proposto.
> A validação técnica por enfermeiro e pedagógica é pré-requisito para publicar conteúdo
> (RM-01, seção 6).
