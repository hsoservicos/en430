# 🎓 EN_430 — Sistema de Avaliação • Introdução à Enfermagem

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat&logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-3.x-003B57?style=flat&logo=sqlite&logoColor=white)
![Tests](https://img.shields.io/badge/Tests-141%20%E2%9C%85-2ea44f?style=flat)
![Coverage](https://img.shields.io/badge/Coverage-86.59%25-2ea44f?style=flat)
![PHPUnit](https://img.shields.io/badge/PHPUnit-11.x-007EC6?style=flat)
![Apache](https://img.shields.io/badge/Apache-2.4%2B-D22128?style=flat&logo=apache&logoColor=white)
![License](https://img.shields.io/badge/License-Educational-blue?style=flat)

</div>

> **Plataforma web para aplicação de avaliações online** da disciplina **Introdução à Enfermagem (EN_430)** do curso Técnico em Enfermagem — EAD/Subsequente.

---

## 📋 Índice

1. [Sobre o Projeto](#1-sobre-o-projeto)
2. [Tecnologias](#2-tecnologias)
3. [Arquitetura](#3-arquitetura)
4. [Estrutura do Repositório](#4-estrutura-do-repositório)
5. [Instalação Rápida](#5-instalacao-rapida)
6. [Instalação Detalhada](#6-instalação-detalhada)
7. [Uso do Sistema](#7-uso-do-sistema)
8. [Banco de Dados](#8-banco-de-dados)
9. [Testes Automatizados](#9-testes-automatizados)
10. [Code Coverage](#10-code-coverage)
11. [Manutenção](#11-manutenção)
12. [Solução de Problemas](#12-solução-de-problemas)
13. [Equipe e Créditos](#13-equipe-e-créditos)

---

## 1. Sobre o Projeto

### 1.1. Contexto

O **Sistema de Avaliação EN_430** foi desenvolvido para substituir a versão original em Python/Flask por uma arquitetura mais simples e portável em **PHP 8 + SQLite + Apache**. A reformulação completa foi realizada para eliminar dependências complexas (Flask, mod_wsgi, ambiente virtual Python) e permitir implantação em servidores compartilhados com suporte nativo a PHP.

### 1.2. Funcionalidades

**Para Estudantes:**
- ✅ **Cadastro** com validação de email e senha criptografada (bcrypt cost 12)
- ✅ **Login** seguro com proteção CSRF em todos os formulários
- ✅ **Avaliações** com 20 questões aleatórias por módulo/nível de dificuldade
- ✅ **Resultado imediato** com gabarito detalhado e badge de desempenho
- ✅ **Progresso** com gráficos por módulo e filtro por dificuldade
- ✅ **Recuperação de senha** via nome + telefone cadastrados

**Para Administradores:**
- 👤 Painel administrativo com proteção por senha mestra
- 📊 Estatísticas gerais (estudantes, avaliações, questões por módulo)
- 📝 Visualização de questões cadastradas
- 🗄️ Backup do banco SQLite

**Técnicas:**
- 🛡️ Proteção CSRF em todos os formulários (tokens por sessão)
- 🔒 Senhas com hash bcrypt (cost 12)
- 🔐 Regeneração de session ID após login (prevenção session fixation)
- 📱 Layout responsivo
- 📈 Gráficos com Chart.js

### 1.3. Público-alvo

- **Estudantes** do curso Técnico em Enfermagem (EAD/Subsequente)
- **Professores** da disciplina Introdução à Enfermagem (EN_430)
- **Administradores** do ambiente virtual de aprendizagem

---

## 2. Tecnologias

| Tecnologia | Versão | Finalidade |
|------------|:------:|:-----------|
| **PHP** | 8.1+ | Linguagem principal (servidor) |
| **SQLite** | 3.x (embutido no PHP) | Banco de dados via PDO |
| **Apache** | 2.4+ | Servidor web (mod_php) |
| **HTML5 / CSS3** | — | Interface do usuário |
| **JavaScript** | ES6+ | Interações cliente-side |
| **Chart.js** | 3.x | Gráficos de progresso |
| **PHPUnit** | 11.x | Testes automatizados |
| **Xdebug** | 3.x | Code coverage |
| **Composer** | 2.x | Gerenciamento de dependências |
| **Git** | — | Controle de versão |

### 2.1. Dependências do Sistema

**PHP Extensions:**
- `pdo_sqlite` — Conexão com banco SQLite
- `mbstring` — Manipulação de strings UTF-8
- `session` — Gerenciamento de sessão
- `json` — Manipulação de dados JSON
- `bcrypt` (password_hash) — Criptografia de senhas

**Apache Modules:**
- `mod_rewrite` — URL rewriting
- `mod_headers` — Headers HTTP
- `mod_expires` — Cache de assets
- `mod_deflate` — Compressão

---

## 3. Arquitetura

### 3.1. Fluxo de Requisição

```
Navegador
    │
    ▼
  .htaccess / router.php
    │  (reescreve URL para index.php?url=...)
    ▼
  index.php (Front Controller)
    │
    ├─► config.php (constantes)
    ├─► functions.php (funções auxiliares)
    ├─► db.php (conexão PDO SQLite)
    │
    ├─► initSession()
    │
    ├─► Switch de Rotas
    │   ├── GET  / → view('index')
    │   ├── POST /cadastro → handleCadastro()
    │   ├── POST /login → handleLogin()
    │   ├── GET/POST /painel → handlePainel()
    │   ├── GET/POST /avaliacao/* → handle*()
    │   ├── GET /admin → handleAdmin()
    │   └── 404 → http_response_code(404)
    │
    └─► view() → require template
```

### 3.2. Camadas

| Camada | Arquivo | Responsabilidade |
|:-------|:--------|:-----------------|
| **Front Controller** | `index.php` | Roteamento + Handlers |
| **Configuração** | `config.php` | Constantes, caminhos, secrets |
| **Database** | `db.php` | Conexão PDO SQLite |
| **Helpers** | `functions.php` | Auth, CSRF, Flash, URL, View |
| **Views** | `views/*.php` | Templates HTML/PHP |
| **Assets** | `assets/css/`, `assets/js/` | Estilos e scripts |
| **Testes** | `tests/*.php` | PHPUnit (5 suites) |

### 3.3. Segurança

- ✅ **CSRF** — Token único por sessão, validado em todos POST
- ✅ **Senhas** — Hash bcrypt com cost 12
- ✅ **Session Fixation** — Regeneração de session ID após login
- ✅ **XSS** — htmlspecialchars() em todas saídas
- ✅ **SQL Injection** — Prepared statements (PDO)
- ✅ **Session Lifetime** — 24 horas de inatividade

---

## 4. Estrutura do Repositório

```
en430/
│
├── README.md                           # 📋 Este documento
├── .gitignore                          # 🔒 Regras de versionamento
│
├── sistema_avaliacao/php/              # 🎯 Aplicação principal
│   ├── index.php                       # Front controller (16.67% coverage)
│   ├── config.php                      # Configuração
│   ├── db.php                          # Conexão SQLite
│   ├── functions.php                   # Funções auxiliares (86.59% coverage)
│   ├── router.php                      # Roteador para dev server
│   ├── .htaccess                       # Rewrite rules (Apache)
│   ├── avaliacao.db                    # Banco SQLite (2.475 questões)
│   │
│   ├── assets/                         # 🎨 Estáticos
│   │   ├── css/style.css               #   Folha de estilos
│   │   └── js/app.js                   #   JavaScript
│   │
│   ├── views/                          # 📄 Templates
│   │   ├── index.php                   #   Página inicial
│   │   ├── cadastro.php                #   Cadastro
│   │   ├── login.php                   #   Login
│   │   ├── painel.php                  #   Dashboard
│   │   ├── avaliacao.php               #   Questões
│   │   ├── resultado.php               #   Gabarito
│   │   ├── progresso.php               #   Gráficos
│   │   ├── recuperar_acesso.php        #   Recuperar senha
│   │   ├── admin.php                   #   Painel admin
│   │   └── admin_login.php             #   Login admin
│   │
│   ├── scripts/                        # 🔧 Utilitários
│   │   ├── recriar_questoes.php        #   Recria banco com 2.475 questões
│   │   ├── questions_data.php          #   Dados das questões
│   │   └── make_coverage.sh            #   Script de code coverage
│   │
│   ├── tests/                          # 🧪 Testes
│   │   ├── bootstrap.php               #   Bootstrap
│   │   ├── AuthTest.php                #   Autenticação (29 testes)
│   │   ├── DatabaseTest.php            #   Banco (4 testes)
│   │   ├── AvaliacaoTest.php           #   Avaliação (23 testes)
│   │   ├── FunctionsUtilTest.php       #   Funções (66 testes)
│   │   ├── FrontControllerTest.php     #   Front controller (14 testes)
│   │   └── IntegrationTest.php         #   HTTP integration (5 testes)
│   │
│   ├── vendor/                         # 📦 Composer (PHPUnit)
│   ├── composer.json / composer.lock   # 📦 Dependências
│   └── phpunit.xml                     # ⚙️ Config do PHPUnit
│
├── GUIA_PUBLICACAO_APACHE.md           # 🐧 Guia completo — Linux
├── GUIA_PUBLICACAO_APACHE_WINDOWS.md   # 🪟 Guia completo — Windows
├── checklist_verificacao.md            # ✅ Checklist pós-deploy
│
├── instalar_publicar_linux.sh          # 🐧 Script instalação Linux
├── instalar_publicar_windows.ps1       # 🪟 Script instalação Windows
├── instalar_publicar_windows.bat       # 🪟 Launcher Windows
│
└── *.html / *.pdf                      # 📚 Materiais didáticos
```

---

## 5. Instalação Rápida

### 5.1. Servidor PHP Embutido (Desenvolvimento/Teste)

```bash
# 1. Clonar o repositório
git clone https://github.com/hsoservicos/en430.git
cd en430

# 2. Verificar PHP
php -v   # Requer PHP 8.1+

# 3. Iniciar servidor de desenvolvimento
cd sistema_avaliacao/php
php -S 127.0.0.1:8080 -t . router.php

# 4. Acessar: http://127.0.0.1:8080/
```

### 5.2. Linux (Apache)

```bash
# 1. Instalar dependências
sudo apt install -y php php-sqlite3 php-mbstring php-xml libapache2-mod-php

# 2. Copiar projeto
sudo mkdir -p /var/www/enfermagem
sudo cp -r sistema_avaliacao /var/www/enfermagem/

# 3. Ativar módulos e reiniciar Apache
sudo a2enmod rewrite headers expires deflate
sudo systemctl restart apache2

# 4. Criar banco de dados
cd /var/www/enfermagem/sistema_avaliacao/php
php scripts/recriar_questoes.php
sudo chown -R www-data:www-data /var/www/enfermagem

# 5. Acessar: http://localhost/sistema_avaliacao/php/
```

### 5.3. Windows (Apache)

```powershell
# 1. Instalar PHP + Apache (ver GUIA_PUBLICACAO_APACHE_WINDOWS.md)
# 2. Copiar projeto para C:\Apache24\htdocs\en430\
# 3. Criar banco de dados
cd C:\Apache24\htdocs\en430\sistema_avaliacao\php
php scripts\recriar_questoes.php
# 4. Acessar: http://localhost/en430/sistema_avaliacao/php/
```

### 5.4. Scripts Automatizados

| Sistema | Script | Modo de Uso |
|:--------|:-------|:------------|
| 🐧 Linux | `instalar_publicar_linux.sh` | `sudo ./instalar_publicar_linux.sh` |
| 🪟 Windows | `instalar_publicar_windows.ps1` | PowerShell (Admin) |
| 🪟 Windows | `instalar_publicar_windows.bat` | Clique duplo |

---

## 6. Instalação Detalhada

Para instruções passo a passo, consulte os guias específicos:

| Guia | Sistema | Conteúdo |
|:-----|:--------|:---------|
| [`GUIA_PUBLICACAO_APACHE.md`](GUIA_PUBLICACAO_APACHE.md) | 🐧 **Linux** | Apache + PHP 8 + SQLite |
| [`GUIA_PUBLICACAO_APACHE_WINDOWS.md`](GUIA_PUBLICACAO_APACHE_WINDOWS.md) | 🪟 **Windows** | Apache + PHP 8 + SQLite |

Após a instalação, utilize o **[checklist_verificacao.md](checklist_verificacao.md)** para validar cada etapa.

---

## 7. Uso do Sistema

### 7.1. Rotas da Aplicação

| Rota | Método | Descrição | Requer Login |
|:-----|:------:|:----------|:------------:|
| `/` | GET | Página inicial | ❌ |
| `/cadastro` | GET/POST | Cadastro de estudante | ❌ |
| `/login` | GET/POST | Login | ❌ |
| `/logout` | GET | Logout | ❌ |
| `/recuperar-acesso` | GET/POST | Recuperação de senha | ❌ |
| `/redefinir-senha` | POST | Redefinição de senha | ❌ |
| `/painel` | GET | Dashboard do estudante | ✅ |
| `/nova-avaliacao` | POST | Gerar nova avaliação | ✅ |
| `/avaliacao/{id}` | GET | Responder avaliação | ✅ |
| `/avaliacao/{id}/responder` | POST | Submeter respostas | ✅ |
| `/avaliacao/{id}/resultado` | GET | Resultado da avaliação | ✅ |
| `/progresso` | GET | Gráficos de progresso | ✅ |
| `/admin` | GET | Painel administrativo | ✅ (admin) |
| `/admin-login` | GET/POST | Login administrativo | ❌ |

### 7.2. Fluxo Completo

```
                        ┌──────────┐
                        │  Home /  │
                        └────┬─────┘
                             │
                    ┌────────┴────────┐
                    ▼                 ▼
             ┌──────────┐      ┌──────────┐
             │ Cadastro │      │  Login   │
             └────┬─────┘      └────┬─────┘
                  │                  │
                  └────────┬─────────┘
                           ▼
                    ┌──────────┐
                    │  Painel  │
                    └────┬─────┘
                         │
              ┌──────────┼──────────┐
              ▼          ▼          ▼
       ┌──────────┐ ┌────────┐ ┌──────────┐
       │Avaliação │ │Progresso│ │ Recuperar│
       └────┬─────┘ └────────┘ │  Senha   │
            │                  └──────────┘
            ▼
       ┌──────────┐
       │ Resultado│
       │+ Gabarito│
       └──────────┘
```

### 7.3. Credenciais Padrão

> **Atenção:** Altere as senhas padrão antes de usar em produção!

| Perfil | Campo | Valor Padrão |
|:-------|:------|:-------------|
| **Admin** | Senha | `admin_enfermagem_2026` |
| **Estudante** | — | Criado via cadastro |

As senhas são configuradas no arquivo `sistema_avaliacao/php/config.php`:
```php
define('ADMIN_SECRET', getenv('ADMIN_SECRET') ?: 'admin_enfermagem_2026');
```

---

## 8. Banco de Dados

### 8.1. Esquema Relacional

```sql
-- Tabela: estudantes
CREATE TABLE estudantes (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    nome            TEXT NOT NULL,
    data_nascimento TEXT,
    telefone        TEXT,
    email           TEXT UNIQUE NOT NULL,
    senha_hash      TEXT NOT NULL,         -- bcrypt cost 12
    data_cadastro   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela: questoes (banco de questões)
CREATE TABLE questoes (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    modulo      INTEGER NOT NULL CHECK(modulo BETWEEN 1 AND 10),
    dificuldade TEXT NOT NULL DEFAULT 'Médio'
                  CHECK(dificuldade IN ('Fácil','Médio','Difícil')),
    texto       TEXT NOT NULL,
    opcao_a     TEXT NOT NULL,
    opcao_b     TEXT NOT NULL,
    opcao_c     TEXT NOT NULL,
    opcao_d     TEXT NOT NULL,
    resposta    TEXT NOT NULL CHECK(resposta IN ('A','B','C','D'))
);

-- Tabela: avaliacoes
CREATE TABLE avaliacoes (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    estudante_id    INTEGER NOT NULL,
    data_inicio     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_fim        TIMESTAMP,
    total_questoes  INTEGER NOT NULL,
    questoes_ids    TEXT NOT NULL,          -- JSON array de IDs
    respostas       TEXT,                  -- JSON object {qid: resposta}
    resultado       TEXT,                  -- JSON object detalhado
    pontuacao       INTEGER,
    status          TEXT DEFAULT 'em_andamento'
                      CHECK(status IN ('em_andamento','concluido')),
    FOREIGN KEY (estudante_id) REFERENCES estudantes(id)
);
```

### 8.2. Distribuição das Questões

| Módulo | Tema | Fácil | Médio | Difícil | Total |
|:------:|:-----|:----:|:-----:|:-------:|:-----:|
| 1 | Fundamentos da Enfermagem | 240 | 120 | 150 | **510** |
| 2 | Processo de Enfermagem (SAE) | 150 | 50 | 50 | **250** |
| 3 | Terminologias e Sinais Vitais | 145 | 55 | 50 | **250** |
| 4 | Farmacologia | 115 | 55 | 45 | **215** |
| 5 | Administração de Medicamentos | 105 | 65 | 45 | **215** |
| 6 | Curativos e Coberturas | 105 | 65 | 50 | **220** |
| 7 | Feridas e Lesões de Pele | 110 | 65 | 50 | **225** |
| 8 | Cuidados com Queimaduras | 90 | 55 | 55 | **200** |
| 9 | Emergência e Urgência | 90 | 65 | 60 | **215** |
| 10 | Ética e Cuidados Paliativos | 75 | 50 | 50 | **175** |
| | **Total** | **1.225** | **645** | **605** | **2.475** |

### 8.3. Comandos Úteis

```bash
# Verificar integridade do banco
php -r "
\$db = new PDO('sqlite:sistema_avaliacao/php/avaliacao.db');
echo 'Integridade: ' . \$db->query('PRAGMA integrity_check')->fetchColumn() . PHP_EOL;
echo 'Questões: ' . \$db->query('SELECT COUNT(*) FROM questoes')->fetchColumn() . PHP_EOL;
echo 'Estudantes: ' . \$db->query('SELECT COUNT(*) FROM estudantes')->fetchColumn() . PHP_EOL;
echo 'Avaliações: ' . \$db->query('SELECT COUNT(*) FROM avaliacoes')->fetchColumn() . PHP_EOL;
"

# Backup do banco
cp sistema_avaliacao/php/avaliacao.db backup_$(date +%Y%m%d_%H%M%S).db

# Recriar banco (⚠️ apaga dados existentes)
cd sistema_avaliacao/php && php scripts/recriar_questoes.php
```

---

## 9. Testes Automatizados

### 9.1. Instalação

```bash
cd sistema_avaliacao/php
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php && php -r "unlink('composer-setup.php');"
php composer.phar install
```

### 9.2. Execução

```bash
# Todos os testes
php vendor/bin/phpunit --no-coverage

# Suite específica
php vendor/bin/phpunit --no-coverage --filter FunctionsUtil
php vendor/bin/phpunit --no-coverage --filter FrontController
php vendor/bin/phpunit --no-coverage --filter Integration
```

### 9.3. Suítes de Teste

| Suite | Arquivo | Testes | O que cobre |
|:------|:--------|:------:|:------------|
| **AuthTest** | `tests/AuthTest.php` | 29 | Hash, CSRF, validação |
| **DatabaseTest** | `tests/DatabaseTest.php` | 4 | Conexão, schema |
| **AvaliacaoTest** | `tests/AvaliacaoTest.php` | 23 | Porcentagem, notas |
| **FunctionsUtilTest** | `tests/FunctionsUtilTest.php` | 66 | Flash, URL, datas, sessão |
| **FrontControllerTest** | `tests/FrontControllerTest.php` | 14 | Rotas GET/POST via include |
| **IntegrationTest** | `tests/IntegrationTest.php` | 5 | Fluxo HTTP completo |
| **Total** | | **141** | |

> ℹ️ Os testes exibem **~210 avisos de deprecação** do PHPUnit 11, relacionados ao uso de `@runInSeparateProcess` no `FrontControllerTest` (necessário para testar rotas sem redefinição de funções). Estes avisos não indicam problemas no código da aplicação.

### 9.4. Script de Coverage

```bash
# Usar script automatizado
cd sistema_avaliacao/php
bash scripts/make_coverage.sh           # Gera relatório
bash scripts/make_coverage.sh --serve   # Gera + serve HTTP
bash scripts/make_coverage.sh --clean   # Remove artefatos

# Ou manualmente (requer xdebug)
XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-html=reports/
```

---

## 10. Code Coverage

### 10.1. Cobertura Atual

| Arquivo | Linhas | Funções | Status |
|:--------|:------:|:-------:|:------:|
| **functions.php** | **86.59%** | **80.77%** | ✅ **80%+** |
| **index.php** | **16.67%** | — | 🟡 Em evolução |
| **config.php** | 0.00% | — | ⚪ Setup |
| **db.php** | 0.00% | 0.00% | ⚪ Setup |
| **Geral** | **~22.59%** | — | 🟡 |

> ℹ️ A cobertura geral é impactada pelo `index.php` (402 linhas, ~79% do total), que executa rotas que acessam banco de dados — estas são testadas apenas via `IntegrationTest` (servidor HTTP separado), onde o PHPUnit não consegue rastrear cobertura entre processos.

### 10.2. Evolução da Cobertura (functions.php)

```
Etapa 1 (testes básicos):    30.95%  ────────┐
Etapa 2 (+ FunctionsUtil):   65.48%  ────────┤
Etapa 3 (+ borda/sessão):    76.19%  ────────┤
Etapa 4 (+ redirectUrl):     77.38%  ────────┤
Etapa 5 (+ url sem static):  86.59%  ────────┘ ✅ 80%+
```

---

## 11. Manutenção

### 11.1. Atualização do Código

```bash
git pull origin master
# Ajustar permissões se necessário
sudo chown -R www-data:www-data sistema_avaliacao/php
sudo chmod 664 sistema_avaliacao/php/avaliacao.db
```

### 11.2. Logs

```bash
# Linux (Apache)
sudo tail -f /var/log/apache2/error.log
sudo tail -f /var/log/apache2/access.log

# Linux (PHP built-in server)
cat /tmp/php8080.log

# Windows (Apache)
type C:\Apache24\logs\error.log
```

### 11.3. Verificação de Integridade

```bash
# Usar o checklist de verificação
cat checklist_verificacao.md

# Verificar banco
php -r "
\$db = new PDO('sqlite:sistema_avaliacao/php/avaliacao.db');
echo 'OK: ' . \$db->query('PRAGMA integrity_check')->fetchColumn() . PHP_EOL;
"
```

### 11.4. Adicionar Novas Questões

Edite o arquivo `sistema_avaliacao/php/scripts/questions_data.php` e execute:

```bash
cd sistema_avaliacao/php
php scripts/recriar_questoes.php
```

> ⚠️ **Atenção:** Recriar o banco apaga todos os dados de estudantes e avaliações existentes.

---

## 12. Solução de Problemas

### ❌ HTTP 500 — Página em Branco

```bash
# 1. Verificar permissão do banco
sudo chmod 664 sistema_avaliacao/php/avaliacao.db
sudo chown -R www-data:www-data sistema_avaliacao/php

# 2. Ativar debug temporário (config.php)
define('DEBUG', true);

# 3. Verificar extensões PHP
php -m | grep -E 'pdo_sqlite|mbstring|session|json'
```

### ❌ CSRF Token Mismatch

- Recarregue a página para gerar um novo token
- Verifique se os cookies de sessão estão habilitados
- O tempo máximo de sessão é 24 horas

### ❌ Rota Não Encontrada (404)

- Verifique se o `.htaccess` está presente
- Apache: `sudo a2enmod rewrite && sudo systemctl restart apache2`
- Servidor embutido: use o `router.php` com `-t . router.php`

### ❌ Banco de Dados — No Such Table

```bash
cd sistema_avaliacao/php
php scripts/recriar_questoes.php
```

### ❌ PHPUnit — Testes Falhando

```bash
# Verificar integração (pode ser falha do servidor HTTP)
php vendor/bin/phpunit --no-coverage --filter '!Integration'

# Reinstalar dependências
php composer.phar install --no-interaction
```

### ❌ Porta em Uso

```bash
# Linux
sudo lsof -ti:8080 | xargs kill -9

# Windows (PowerShell)
Get-Process -Id (Get-NetTCPConnection -LocalPort 8080).OwningProcess | Stop-Process
```

---

## 13. Equipe e Créditos

| Papel | Responsável |
|:------|:------------|
| **Disciplina** | Introdução à Enfermagem (EN_430) |
| **Curso** | Técnico em Enfermagem — EAD/Subsequente |
| **Ano** | 2026 |
| **Linguagem** | PHP 8 + JavaScript |
| **Banco de Dados** | SQLite (2.475 questões, 10 módulos) |
| **Testes** | PHPUnit — 141 testes, 350 asserções |
| **Repositório** | [github.com/hsoservicos/en430](https://github.com/hsoservicos/en430) |

### Documentos Relacionados

| Documento | Descrição |
|:----------|:----------|
| [`GUIA_PUBLICACAO_APACHE.md`](GUIA_PUBLICACAO_APACHE.md) | 🐧 Instalação completa em Linux (Apache + PHP 8) |
| [`GUIA_PUBLICACAO_APACHE_WINDOWS.md`](GUIA_PUBLICACAO_APACHE_WINDOWS.md) | 🪟 Instalação completa em Windows (Apache + PHP 8) |
| [`checklist_verificacao.md`](checklist_verificacao.md) | ✅ Checklist pós-deploy |
| [`sistema_avaliacao/php/README.md`](sistema_avaliacao/php/README.md) | 📋 Documentação técnica do sistema PHP |

---

<div align="center">

*Documentação gerada em Julho de 2026 • EN_430 — Introdução à Enfermagem*  
*PHP 8 + Apache + SQLite • 🎓 Material organizado para Estudos e Aprendizado*

</div>
