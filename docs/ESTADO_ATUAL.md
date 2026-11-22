# 📍 Estado Atual do Projeto — EN_430

**Atualizado em:** 2026-11-22
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
| **Porta** | 80 (Apache + PHP 8.6) |
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
| Casos de teste | **180** |
| Asserções documentadas | **466** |
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

---

## 8. Consentimento, privacidade e exclusão de conta (2026-11-21)

Implementação dos itens abertos da política de privacidade no sistema de avaliação.

| Item | Implementação |
|------|---------------|
| **Checkbox de consentimento** | Obrigatório no cadastro (`views/cadastro.php`); sem ele a conta não é criada |
| **Registro do aceite** | Colunas `estudantes.consentimento_em` e `estudantes.consentimento_versao`; versão em `CONSENTIMENTO_VERSAO` (`config.php` = 1.0) |
| **Rota pública da política** | `/politica-de-privacidade` renderiza `docs/juridico/POLITICA_DE_PRIVACIDADE.md` (copiado para `politica.md` na imagem Docker) |
| **Exclusão pelo titular** | `/excluir-conta`: senha + confirmação textual `EXCLUIR`; apaga conta e avaliações em transação e registra `exclusao_conta_titular` em `admin_log` |
| **Bancos existentes** | `ensureSchemaUpgrades()` adiciona as colunas em tempo de conexão (o entrypoint só migra quando o banco não existe) |
| **Navegação** | Links "Política de Privacidade" e "Excluir minha conta" no painel do estudante |

| Verificação | Resultado |
|-------------|-----------|
| Sintaxe (`php -l`, PHP 8.4) | 12 arquivos alterados/criados, sem erro |
| PHPUnit | **180 testes, 466 asserções, 0 falhas** (consentimento, política, exclusão, reconsentimento e inatividade) |
| Renderização da política | 14.109 bytes de Markdown → 13 tabelas, 37 parágrafos, HTML escapado |

### 8.1. Reconsentimento na mudança de versão

| Item | Implementação |
|------|---------------|
| **Detecção** | `precisaReconsentimento()` compara `consentimento_versao` com `CONSENTIMENTO_VERSAO`; contas sem versão (anteriores ao registro) também reconsentem |
| **Bloqueio** | `/painel`, `/progresso`, `/nova-avaliacao` e `/avaliacao` redirecionam para `/reconsentimento` até o aceite |
| **Aceite** | `/reconsentimento` mostra versão anterior × vigente, exige o checkbox e regrava data + versão |
| **Auditoria** | `consentimento_atualizado` em `admin_log`, com `versao_anterior` e `versao` |
| **Direitos preservados** | `/excluir-conta`, `/politica-de-privacidade` e `/logout` continuam acessíveis sem o aceite |

### 8.2. Retenção de contas inativas (Política, item 8.2)

| Item | Implementação |
|------|---------------|
| **Detecção** | `ultimo_acesso_em` (gravado no login) ou `data_cadastro`, sem acesso há `INATIVIDADE_MESES` = 24 |
| **Aviso** | E-mail com prazo, link de acesso e canal de privacidade; registro em `notificacoes_email` e em `estudantes.inatividade_aviso_em` |
| **Carência** | `INATIVIDADE_GRACA_DIAS` = 30; novo acesso no período cancela o aviso e mantém a conta |
| **Exclusão** | Conta + avaliações, em transação, auditada como `exclusao_conta_inativa` em `admin_log` |
| **Execução** | `php scripts/rotina_inativas.php [--dry-run]`, para cron/Coolify |
| **Painel** | Seção "Retenção de contas inativas" no admin: a avisar, em carência, elegíveis, última execução e últimos avisos |
| **Segurança** | Com `MAIL_TRANSPORT=log` (padrão) nada é entregue e **nenhuma conta é excluída**, porque a exclusão exige `inatividade_aviso_em` gravado |

> **Pendências que seguem abertas:** criar os aliases de e-mail (`privacidade@`, `juridico@`,
> `seguranca@`) e configurar um transporte real de e-mail (`MAIL_TRANSPORT=mail` ou provedor)
> para que os avisos de inatividade sejam entregues.

### 8.3. Imagem Docker validada (2026-11-22)

Build local e execução em container (`en430:teste`, 768 MB), com volume `/data` novo, exercitando
as rotas por HTTP antes do deploy.

| Rota / verificação | Resultado |
|---|---|
| `GET /` · `/cadastro` · `/login` · `/admin-login` | 200 |
| `GET /politica-de-privacidade` | 200 · 19,8 KB · 13 tabelas renderizadas |
| `POST /cadastro` sem aceite | 200 com erro de consentimento; conta não criada |
| `POST /cadastro` com aceite | 302 → `/login`; `consentimento_versao=1.0` e `consentimento_em` gravados |
| `GET /painel` · `/progresso` · `/excluir-conta` | 200 (logado) |
| `GET /painel` com versão `0.9` | 302 → `/reconsentimento` |
| `POST /reconsentimento` | 302 → `/painel`; banco em `1.0`; log `consentimento_atualizado` |
| `POST /excluir-conta` | 302 → `/`; conta removida; log `exclusao_conta_titular` |
| `GET /admin` | 200 com seção "Retenção de contas inativas (24 meses)" |
| Rotina `rotina_inativas.php` no container | dry-run e execução: 1 candidata (25 meses), aviso `simulado`, 0 exclusões |
| `GET /politica.md` · `/scripts/*.php` | 403 |
| Healthcheck do container | `healthy` |
| PHPUnit | 180 testes, 466 asserções, 0 falhas |

**Defeitos encontrados e corrigidos nesta validação:**

1. **O build da imagem falhava** — `.dockerignore` excluía `docs/`, e o `COPY` da política derrubava o
   build. Corrigido com exceção explícita (`docs/**` + `!docs/juridico/POLITICA_DE_PRIVACIDADE.md`).
   Toda publicação desde a criação da rota `/politica-de-privacidade` estava sem imagem nova.
2. **`/scripts/*.php` era servido por HTTP** — o shebang aparecia como texto e o `403` do guarda de
   CLI não era aplicado. Adicionado `RewriteRule ^scripts/ - [F,L]` ao `.htaccess` e removido o
   shebang do script (executado como `php script.php`).

### 8.4. Segurança do stack (2026-11-22)

Relatório completo: [`docs/auditorias/RELATORIO_SEGURANCA_STACK_2026-11-22.md`](auditorias/RELATORIO_SEGURANCA_STACK_2026-11-22.md).

| Item | Situação |
|---|---|
| PHP | **8.6.0** — migração concluída em 22/11/2026 (antes: 8.4.28); suporte de segurança até 31/12/2030 |
| Apache | **2.4.71** — corrige o RCE crítico CVE-2026-119618 (`mod_http2`, que não está carregado) |
| SAPI | `mod_php` — sem PHP-FPM/CGI, o que exclui CVE-2026-129289 e CVE-2026-129292 |
| Hardening aplicado | `expose_php=Off`, `display_errors=Off` com log em stderr, sessão `HttpOnly` + `use_strict_mode`, `upload_progress=0`, `ServerTokens Prod`, bloqueio de `.xml/.ini/.sh/.sql` por HTTP |
| Compatibilidade | suíte completa em PHP 8.6.0: **180 testes, 466 asserções, 0 falhas e 0 depreciações**; metadados dos testes em attributes (`#[RunInSeparateProcess]`) e `curl_close()` removido |
| Varredura de imagem | `.github/workflows/trivy.yml` — build + Trivy (HIGH/CRITICAL corrigíveis) em push/PR na `master` e semanalmente |
| Deploy | **em produção roda PHP 8.6.0** desde o redeploy de 22/11/2026 (`0cf5ed2`); rotas novas publicadas (ver 8.5) |
| HTTPS na aplicação | `.htaccess` envia HSTS; `requisicaoSegura()` lê `CF-Visitor`/`X-Forwarded-Proto` para marcar o cookie `Secure`. O redirect HTTP→HTTPS fica na borda (Cloudflare), porque o Apache recebe sempre HTTP do túnel |

### 8.5. Verificação de produção, Coolify e Cloudflare (2026-11-22)

Checagem dos serviços de publicação após o endurecimento e a migração para PHP 8.6.

| Serviço / item | Verificação | Resultado |
|---|---|---|
| Coolify | `coolify app get 2iweyqxekxmminhiwgyv5bvk` | `running:healthy`; app `en430.git:master…`, build `dockerfile`, porta 80 |
| Coolify | deploy forçado (`mhtajrncxkmn8oci2e6izkri`) | `finished` no commit `0cf5ed2` (22/11) |
| Coolify | `http://localhost:8000` | HTTP 302 (painel ativo) |
| Produção | `php -v` no container | **PHP 8.6.0** (ZTS); `expose_php=Off`, por isso não há `X-Powered-By` |
| Produção | rotas novas | `/politica-de-privacidade` **200**; `/excluir-conta` e `/reconsentimento` **302 → /login** (exigem sessão), rotas publicadas |
| Cloudflare | DNS (`en430.appservice.tec.br`) | A/AAAA da Cloudflare; NS `kallie`/`kirk.ns.cloudflare.com` |
| Cloudflare | certificado de borda | Google Trust Services `WE1`, `CN=appservice.tec.br`, válido até 11/01/2027 |
| Cloudflare Tunnel | `cloudflared` (systemd) | ativo; **4 conexões HA**, `ready`, borda GRU |
| Cloudflare Tunnel | métricas `:20241` | 4 conexões ativas; erros recentes só para `cpainel` (regra 2), nenhum para `en430` |
| Borda | cabeçalhos | CSP, `X-Frame-Options`, `nosniff` e `Referrer-Policy` presentes |
| Borda | HTTPS | `http://` ainda responde **200** (sem redirect) e **sem HSTS**; o fix de `.htaccess`/cookie está no repositório local, aguardando publicação |

**Conclusões e pendências:**

1. **Deploy resolvido:** o redeploy forçado publicou o commit `0cf5ed2`; produção confirmada em PHP 8.6.0
   e com as rotas de LGPD no ar (política 200; exclusão/reconsentimento 302 para login sem sessão).
2. **HTTPS parcialmente forçado:** o HSTS é enviado pelo `.htaccess` e o cookie de sessão usa `Secure`
   quando `CF-Visitor`/`X-Forwarded-Proto` indicam HTTPS. O redirect HTTP→HTTPS **não** pode ser feito
   no Apache: atrás do túnel ele recebe HTTP mesmo para visitantes em HTTPS e o redirect gera loop
   (constatado em 22/11 e revertido). Falta ativar *Always Use HTTPS* no painel da Cloudflare — a API
   não foi usada por falta de token.
3. **Cloudflare saudável:** DNS, certificado e túnel (4 conexões) operando; a falha registrada em
   `localhost:8000` é da aplicação `cpainel`, não do EN_430.
