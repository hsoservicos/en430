# 📋 Changelog — Sistema de Avaliação EN_430

> **Disciplina:** Introdução à Enfermagem (EN_430)  
> **Repositório:** [github.com/hsoservicos/en430](https://github.com/hsoservicos/en430)

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/spec/v1.0.0.html).

---

## [2.0.0] — 2026-07-16

### 🎉 Reformulação Completa

Migração completa do sistema de Python/Flask para **PHP 8 + Apache + SQLite**.

#### Adicionado

##### 🔧 Core do Sistema
- [x] Front controller (`index.php`) com roteamento de 15 rotas via switch/case
- [x] 14 handlers dedicados para cada funcionalidade (cadastro, login, avaliação, admin, etc.)
- [x] Sistema de autenticação com CSRF, bcrypt e session fixation prevention
- [x] Flash messages com renderização automática nas views
- [x] Tratamento de erros com try/catch e fallback para DEBUG
- [x] Rota 404 com `http_response_code(404)`

##### 🔒 Segurança
- [x] Proteção CSRF em todos os formulários POST (`random_bytes(32)` + `hash_equals()`)
- [x] Senhas com hash bcrypt (cost 12) via `password_hash()`/`password_verify()`
- [x] Session fixation prevention (`session_regenerate_id(true)` pós-login)
- [x] Session cookie com `httponly=true`, `samesite=Lax`, 24h de lifetime
- [x] XSS prevention com `htmlspecialchars()` em 31 pontos de saída
- [x] SQL Injection prevention com 100% prepared statements PDO

##### 🗄️ Banco de Dados
- [x] SQLite via PDO com WAL journal mode e foreign keys
- [x] 2.475 questões distribuídas em 10 módulos com 3 níveis de dificuldade
- [x] Schema com CHECK constraints, índices e view de progresso
- [x] Script `recriar_questoes.php` para recriação do banco

##### 🎨 Interface
- [x] 10 templates PHP com design responsivo (media queries)
- [x] CSS moderno com variáveis, animações e transições
- [x] JavaScript com máscaras de entrada (telefone, CPF, CEP, data)
- [x] Navegação por teclado (Enter para avançar questões)
- [x] IntersectionObserver para animação de barras de progresso
- [x] Seletor visual de dificuldade (Fácil/Médio/Difícil/Todas)

##### 🧪 Testes
- [x] PHPUnit 11.x com 141 testes e 350 asserções
- [x] 6 suítes de teste: Auth (29), Database (4), Avaliacao (23), FunctionsUtil (66), FrontController (14), Integration (5)
- [x] Script `make_coverage.sh` para geração de code coverage
- [x] Code coverage: **86.59%** functions.php, **27.11%** geral

##### 📖 Documentação
- [x] README.md com 13 seções + badges de status
- [x] GUIA_PUBLICACAO_APACHE.md — Guia completo para Linux (13 seções)
- [x] GUIA_PUBLICACAO_APACHE_WINDOWS.md — Guia completo para Windows
- [x] checklist_verificacao.md — 11 seções com 50+ itens de verificação
- [x] RELATORIO_EVOLUCAO.md — Relatório de evolução e progresso
- [x] CHANGELOG.md — Este documento

##### 🚀 Deploy
- [x] Script `instalar_publicar_linux.sh` — Instalação automatizada Linux
- [x] Script `instalar_publicar_windows.ps1` — Instalação automatizada Windows
- [x] Script `instalar_publicar_windows.bat` — Launcher Windows
- [x] .htaccess com URL rewriting + bloqueio de arquivos sensíveis
- [x] router.php para servidor PHP built-in (desenvolvimento)
- [x] phpunit.xml com configuração de testes e coverage
- [x] composer.json com dependências (phpunit 11.x)
- [x] .gitignore completo

### 🔄 Modificado

| Item | Antes | Depois |
|:-----|:------|:--------|
| **Linguagem** | Python 3.10+ (Flask) | PHP 8.1+ |
| **Servidor** | mod_wsgi / Gunicorn | Apache mod_php |
| **Template** | Jinja2 | PHP embutido no HTML |
| **Dependências** | ~10 pacotes pip | 1 pacote Composer (PHPUnit) |
| **Testes** | 0 (inexistente) | 141 testes (350 asserções) |
| **Coverage** | 0% | 86.59% (core) |
| **Documentação** | Inexistente | 6 documentos + scripts |

### ❌ Removido

- [x] Todo o código Python (Flask, Jinja2, WSGI)
- [x] Dependências pip (Flask, Jinja2, python-dotenv, etc.)
- [x] Ambiente virtual Python (venv)
- [x] Arquivos `__pycache__` e `.pyc`
- [x] Referências a Python nos guias de instalação
- [x] Configuração mod_wsgi

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
