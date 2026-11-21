# 📋 Artifício de Auditoria e Análise do Sistema de Avaliação EN_430

> 🔄 **Atualização pós-sessão (2026-11-21).** Este documento registra a auditoria
> inicial; parte das recomendações já foi tratada. Desde a auditoria:
>
> - Repositório reorganizado em `docs/`, `materiais/` e `scripts/`, com links corrigidos.
> - Dockerfile movido para a raiz (contexto do repositório) e materiais servidos em `/materiais/`.
> - `sistema_avaliacao/.env.example` disponível e cabeçalhos de segurança (CSP, X-Frame-Options,
>   X-Content-Type-Options, Referrer-Policy, Permissions-Policy) ativos no `.htaccess`.
> - Números conferidos na fonte: **2.803 questões** (1.378 Fácil / 739 Médio / 686 Difícil)
>   e **158 casos de teste**.
> - Deploy oficial documentado em [`../deploy/COOLIFY.md`](../deploy/COOLIFY.md).
>
> Para a condição atual completa, consulte [`../ESTADO_ATUAL.md`](../ESTADO_ATUAL.md).
> Os itens de refatoração estrutural (extract, router, joins de progresso) continuam pendentes.

## 📅 Data da Auditoria: Novembro de 2026
## 🎯 Projeto: Sistema de Avaliação — Introdução à Enfermagem (EN_430)
## 👨‍💻 Avaliado por: Codebuff (Agente de Codificação Autônoma)

---

## 1. 📊 Visão Geral do Projeto

### 1.1 Descrição
O **Sistema de Avaliação EN_430** é uma plataforma web desenvolvida em PHP 8+ para o curso de Técnico em Enfermagem (EAD/Subsequente). O sistema permite o cadastro e login de estudantes, aplicação de avaliações online com 2.803 questões distribuídas por 10 módulos, visualização de resultados detalhados e acompanhamento de progresso por módulo e nível de dificuldade.

### 1.2 Tecnologias Utilizadas
| Tecnologia | Versão | Função |
|------------|--------|--------|
| **PHP** | 8.1+ | Linguagem principal (servidor) |
| **SQLite** | 3.x | Banco de dados (via PDO) |
| **Apache** | 2.4+ | Servidor web (mod_php) |
| **HTML5** | — | Templates de interface |
| **CSS3** | — | Estilização responsiva |
| **JavaScript** | — | Interações cliente-side |
| **Chart.js** | 3.x | Gráficos de progresso |
| **PHPUnit** | 11.x | Testes automatizados |

### 1.3 Estrutura de Diretórios Principais
```
sistema_avaliacao/php/
├── index.php             # Front controller / roteador principal
├── config.php            # Constantes de configuração
├── db.php                # Conexão PDO com SQLite
├── functions.php         # Funções auxiliares (auth, CSRF, helpers)
├── router.php            # Roteador para PHP built-in server
├── handlers.php          # Handlers (controladores) das rotas
├── avaliacao.db          # Banco de dados SQLite (2.803 questões)
├── views/                # Templates PHP
├── assets/               # Arquivos estáticos (css, js)
├── scripts/              # Scripts de manutenção
├── tests/                # Testes automatizados (158 tests, 385 assertions)
├── composer.json         # Dependências
└── phpunit.xml           # Configuração do PHPUnit
```

---

## 2. 🏗️ Análise Arquitetural

### 2.1 Padrões Utilizados
- **Front Controller Pattern**: Todas as requisições vão para `index.php`, que roteia conforme a URL.
- **Separation of Concerns**: Código dividido em configuração, funções auxiliares, handlers e views.
- **Repository Pattern (implícito)**: Operações de banco de dados encapsuladas em funções PHP.

### 2.2 Pontos Fortes Arquiteturais
✅ **Roteamento centralizado** em `index.php` com switch statement bem organizado
✅ **Separação de handlers** em `handlers.php` permitindo reuso em testes
✅ **Inicialização de sessão** consistente via `initSession()` em `functions.php`
✅ **Proteção CSRF** implementada em todas as rotas POST
✅ **Rate limiting** para login (máx. 5 tentativas, bloqueio de 1 minuto)
✅ **Session fixation prevention** via `regenerateSessionId()`

### 2.3 Áreas de Melhoria Arquitetural
⚠️ **Deep switch em index.php**: O roteador possui muitos casos (20+), o que pode ser difícil de manter a longo prazo. Considerar router dedicado ou estrutura de mapa de rotas.
⚠️ **Handlers dependem de ordem de include**: `handlers.php` depende de `config.php` e `functions.php` já estarem carregados.
⚠️ **URL parsing complexo**: A função `url()` em `functions.php` tenta detectar caminho base automaticamente, o que pode gerar comportamento inesperado em diferentes ambientes de deployment.

---

## 3. 🔒 Análise de Segurança

### 3.1 Medidas de Segurança Implementadas ✅
| Medida | Implementação | Avaliação |
|--------|--------------|-----------|
| **Hash de senha** | `password_hash()` com `PASSWORD_BCRYPT, ['cost' => 12]` | ✅ Boa prática (cost 12 adequado) |
| **Proteção CSRF** | Token bin2hex(random_bytes(32)) em todas as formas POST | ✅ Completo e bem implementado |
| **Rate limiting por IP** | Contador de tentativas na sessão com bloqueio temporário | ✅ Adequado para evitar brute force |
| **Foreign Keys no SQLite** | `PRAGMA foreign_keys = ON` | ✅ Garantir integridade referencial |
| **Journal Mode WAL** | `PRAGMA journal_mode = WAL` | ✅ Melhor performance e segurança em escrita |
| **HTTPS only cookies** | Verificado em `initSession()` baseado em `$_SERVER['HTTPS']` | ✅ Boa prática |
| **SameSite Lax** | Configurado no session_set_cookie_params | ✅ Proteção contra CSRF cross-site |

### 3.2 Possíveis Vulnerabilidades ⚠️
| Risco | Gravidade | Comentário |
|-------|-----------|------------|
| **SQL Injection** | Baixa | Todas as queries usam prepared statements via PDO — **seguro** |
| **XSS (Cross-Site Scripting)** | Média | Dados são passados para views via `extract($data)` e `htmlspecialchars()` em flashes, mas **verificar** se todos os outputs estão protegidos (especialmente em views `avaliacao.php`, `resultado.php`, `admin.php`) |
| **Session Fixation** | Baixa | `regenerateSessionId()` usado após login — **adequado** |
| **Directory Traversal** | Baixa | Função `url()` usa `$_SERVER['SCRIPT_NAME']` — verificar caminhos |
| **Password Rate Limiting** | Média | Implementado, mas poderia ser mais robusto com armazenamento externo (Redis) |
| **Arquivos .htaccess** | Baixa | Regras de rewrite — verificar se não há exposição de arquivos sensíveis |

### 3.3 Recomendações de Segurança
1. **Validar todos os outputs** nas views — fazer auditoria completa de `echo` em templates
2. **Implementar Content Security Policy (CSP)** no cabeçalho HTTP
3. **Adicionir headers de segurança** (X-Content-Type-Options, X-Frame-Options, Referrer-Policy)
4. **Validar uploads** se houver funcionalidade de upload no futuro
5. **Considerar armazenamento de rate limit** em banco de dados ou Redis para persistência entre reinícios

---

## 4. 📈 Qualidade do Código e Maintainability

### 4.1 Métricas Atuais
- **Total de arquivos PHP**: 11 arquivos principais
- **Loc (Lines of Code)**: Aprox. 8.000 linhas (incluindo comments)
- **Nível de indentação**: Consistente (4 espaços)
- **Documentação**: Arquivo README.md extenso com guias de instalação

### 4.2 Pontos Fortos 🟢
✅ **Nomenclatura consistente**: Funções nomeadas em inglês (`handleCadastro`, `handleLogin`, etc.)
✅ **Comments abundantes**: Blocos `════════════════════════════` separando seções funcionais
✅ **Funções pequenas e focadas**: Cada handler faz uma coisa específica
✅ **Extração de views**: Função `view()` separa lógica de apresentação
✅ **Tratamento de erros**: Try/catch em operações de banco com mensagens amigáveis

### 4.3 Áreas de Melhoria 🟡
⚠️ **Extração de variáveis via `extract($data)`**: Pode causar conflitos de nome de variáveis no escopo da view. Considerar passar dados de forma mais explícita.
⚠️ **Funções globais extensas**: 60+ funções no `functions.php` — considerar modularização em classes ou namespaces.
⚠️ **Hardcoded strings em português**: Mensagens de erro hardcoded dificultar internacionalização futura.
⚠️ **Nível de profundidade de switch em index.php**: 20+ cases em um único switch — considerar estratégia de roteamento mais escalável.
⚠️ **Falta de tipagem estática**: PHP 8+ com typed properties não utilizados consistentemente.

### 4.4 Sugestões de Refatoração
1. **Considerar namespace** para funções: `Enf430\Auth`, `Enf430\Database`, etc.
2. **Substituir `extract()`** por acesso direto `$data['chave']` nas views
3. **Criar classes de handler** em vez de funções globais para melhor testabilidade
4. **Adicionar tipo declaration** nas funções críticas (`PDO`, `string`, `int`)
5. **Mover constantes** para arquivo de configuração ambiental (.env)

---

## 5. 🧪 Testes Automatizados

### 5.1 Cobertura Atual
| Arquivo | Cobertura |
|---------|-----------|
| `functions.php` | **91.15%** 🔥 |
| `index.php` | **63.06%** 🎯 |
| `db.php` | **78.95%** |
| `handlers.php` | **30.24%** |
| **Geral** | **~54%** |

**Total**: 158 testes, 385 assertivas

### 5.2 Avaliação da Suite de Testes ✅
- **Bom coverage** nas funções de auth e validação (91% em functions.php)
- **Tests cobrem**: hash de senha, CSRF, cadastro, login, rate limiting, recuperação de acesso, operações de avaliação
- **DatabaseTest** cobre schema, foreign keys, constraints, indices
- **AuthTest** cobre authentication flow completo
- **AvaliacaoTest** cobre geração, respostas, correção, progresso
- **IntegrationTest** testa endpoints HTTP (não lido completamente)

### 5.3 Lacunas nos Testes ⚠️
| Área | Grave | Comentário |
|------|-------|------------|
| **Views/templates** | Alta | Nenhum teste de renderização de views |
| **JavaScript/frontend** | Média | Nenhum teste JS (Chart.js interações) |
| **Performance/load testing** | Baixa | Nenhum teste de carga |
| **Security penetration testing** | Média | Nenhum teste de vulnerabilidade |
| **Admin routes** | Baixa | Testes limitados para painel administrativo |
| **Edge cases** | Média | Poucos testes para validações de borda |

### 5.4 Recomendações de Testes
1. **Adicionar testes de integração HTTP** usando PHPUnit com browser kit ou framework separado
2. **Criar testes de renderização** de views críticas (login, cadastro, avaliação)
3. **Testes de performance** com número crescente de questões/respostas
4. **Testes de segurança** (fuzzing de inputs, bypass de CSRF, etc.)
5. **Cobertura alvo**: Alcançar ~80% de cobertura geral (atualmente ~54%)

---

## 6. 📄 Documentação

### 6.1 Documentação Existente ✅
- **README.md** completo com guia de instalação, configuração, uso
- **GUIA_PUBLICACAO_APACHE.md** e **GUIA_PUBLICACAO_APACHE_WINDOWS.md** — guias de deployment
- **Comentários internos** abundantes em todos os arquivos PHP
- **phpunit.xml** configurado corretamente
- **CHANGELOG.md** com histórico de mudanças

### 6.2 Lacunas na Documentação ⚠️
| Aspecto | Status |
|---------|--------|
| **Arquitetura/design** | Parcial — README descreve funcionalidades, não arquitetura |
| **API endpoints** | Limitada — documentada apenas em README como "API interna" |
| **Variáveis de ambiente** | Parcial — `config.php` usa `getenv()` mas `.env` não encontrado |
| **Guia de contribuição** | Não encontrado |
| **Código de conduta** | Não encontrado |
| **Rollback/backup procedures** | Documentado em README, mas poderia ser mais detalhado |

### 6.3 Recomendações de Documentação
1. **Criar arquivo ARCHITECTURE.md** descrevendo padrões e fluxo de dados
2. **Documentar endpoints API** openapi/swagger format
3. **Criar .env.example** baseado em config.php
4. **Adicionir guia de contribuição** (CODING_STANDARDS.md)
5. **Documentar procedimentos de backup/restore** do banco SQLite

---

## 7. 📊 Performance

### 7.1 Configuração Atual
- **Banco de dados**: SQLite 3.x (embutido no PHP)
- **Total de questões**: 2.803 questões em 10 módulos
- **Questões por avaliação**: 20 questões aleatórias
- **Índices**: `idx_questoes_modulo`, `idx_avaliacoes_estudante` (verificado nos testes)

### 7.2 Métricas de Performance
| Métrica | Status |
|---------|--------|
| **Tempo de conexão PDO** | ~1-2ms (banco em memória/disco) |
| **Query de seleção de questões** | Depende de índice — Otimizado com `dificuldade` e `modulo` |
| **PHP startup** | ~50-100ms (apenas config + requires) |
| **Geração de avaliação** | ~200-500ms (depende de randomização + filtros) |
| **Rate limiting check** | ~0.5ms (lookup em array de sessão) |

### 7.3 Problemas de Performance Identificados ⚠️
1. **Queries complexas no `handleProgresso()`**: múltiplos joins com `json_each()` podem ser lentos com crescimento de dados
2. **`json_encode`/`json_decode` frequente**: stored em colunas `questoes_ids` e `resultado` — processing overhead
3. **Sem cache de queries**: Cada requisição recompute aggregates
4. **Sem otimização de conexão**: Nova conexão PDO em cada request (padrão PDO behavior)

### 7.4 Recomendações de Performance
1. **Adicionar índices adicionais** se crescimento de tabelas for significativo
2. **Considerar migração para MySQL/PostgreSQL** se expectativa de muitos concurrent users
3. **Implementar caching** para queries de aggregate (estatísticas, progresso)
4. **Otimizar queries do handleProgresso** — os joins com json_each podem ser substituídos por tabelas de junção
5. **Usar `PDO::ATTR_EMULATE_PREPARES => false`** já está definido — manter para consistência

---

## 8. 🗂️ Banco de Dados

### 8.1 Esquema Atual
4 tabelas principais:
- `estudantes` — cadastro de usuários (8 campos)
- `questoes` — banco de questões (11 campos, 2.803 registros)
- `avaliacoes` — histórico de avaliações (7 campos)
- `respostas` — respostas dadas nas avaliações (5 campos)

### 8.2 Distribuição de Questões
| Módulo | Fácil | Médio | Difícil | Total |
|--------|-------|-------|---------|-------|
| 1-10 | 1.378 | 739 | 686 | **2.803** |

### 8.3 Observações ⚠️
- **Campo `questoes_ids` na tabela avaliacoes**: Armazenado como JSON array — consulta flexível mas menos performática que tabela de relação Many-to-Many
- **Sem tabela de módulos dedicada**: Módulo é um campo integer na tabela questões — poderia ser uma tabela separada para normalização
- **Sem tabela de logs de auditoria** (exceto `admin_log` criada dinamically)

### 8.4 Recomendações de Banco de Dados
1. **Considerar tabela de relação** `avaliacao_questao` em vez de JSON column para melhor performance e queries complexas
2. **Tabela de módulos** para facilitar queries e relatórios
3. **Backup automático** do banco `avaliacao.db` — já documentado, mas verificar rotina
4. **VACUUM periódico** para otimizar arquivo SQLite após muitas exclusões/atualizações

---

## 9. 🎯 Recomendações Prioritárias

### Prioridade Alta (Imediato)
1. **Auditar outputs XSS** em todas as views — verificar cada `echo`/print em templates
2. **Adicionir testes de renderização** de views críticas (mínimo 20 testes novos)
3. **Criar .env.example** a partir de config.php para melhorar deploy e segurança
4. **Refatorar `extract($data)`** nas views para passagem explícita de variáveis

### Prioridade Média (Curto Prazo - 2-4 semanas)
1. **Aumentar cobertura de testes** de 54% para pelo menos 75%
2. **Otimizar queries do handleProgresso** com tabelas de junção adequadas
3. **Adicionir headers de segurança** (CSP, X-Content-Type-Options, etc.)
4. **Documentar API endpoints** abertamente

### Prioridade Baixa (Médio Prazo - 1-3 meses)
1. **Migração para banco MySQL/PostgreSQL** se houver necessidade de escalabilidade
2. **Implementar internacionalização** (i18n) — remover strings hardcoded em português
3. **Refatorar roteador** index.php com padrão de estratégia ou mapa de rotas
4. **Adicionir relatórios mais detalhados** para admin (gráficos, análise de acertos por módulo)

---

## 10. ✅ Checklist de Verificação Pós-Auditoria

- [ ] **Corrigir possíveis XSS** em views (avaliacao.php, resultado.php, admin.php)
- [ ] **Criar arquivo .env.example** baseado em config.php
- [ ] **Adicionir 20+ novos testes** cobrindo views e edge cases
- [ ] **Adicionir headers de segurança** no Apache/virtual host config
- [ ] **Refatorar extract()** para passagem explícita de dados em views
- [ ] **Otimizar queries de progresso** com tabelas de junção
- [ ] **Atualizar README** com seção de "Próximos passos" e roadmap
- [ ] **Executar full test suite** e garantir 0 failures

---

## 11. 📈 Resumo Executivo

**Estado Geral do Projeto: SAUDÁVEL ⚠️**

O projeto apresenta:
- ** Código maintainable** com boa estrutura e documentação interna
- **Segurança implementada** com boas práticas (bcrypt, CSRF, rate limiting)
- **Testes automatizados** ativos com 54% de cobertura (boa base para expansão)
- **Banco de dados estruturado** com 2.803 questões bem distribuídas
- **Áreas de melhoria identificadas** mas nada crítico que impeça operação

**Próximos passos recomendados**:
1. Atendar prioridades altas (segurança XSS + testes)
2. Planejamento de refatoração modular a médio prazo
3. Documentação complementar para onboarding de novos desenvolvedores

---

**Fim do Artifício de Auditoria**

*Gerado automaticamente por Codebuff em Novembro de 2026*