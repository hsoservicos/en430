# 📋 Changelog — Sistema de Avaliação EN_430

> **Disciplina:** Introdução à Enfermagem (EN_430)  
> **Repositório:** [github.com/hsoservicos/en430](https://github.com/hsoservicos/en430)

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/spec/v1.0.0.html).

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
