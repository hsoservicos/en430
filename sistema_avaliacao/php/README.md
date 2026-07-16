# 🌐 Sistema de Avaliação — Introdução à Enfermagem (EN_430)

> **PHP 8 + SQLite + Apache** — Plataforma de avaliação online para o curso de Introdução à Enfermagem.

---

## 📋 Sumário

1. [Sobre o Sistema](#1-sobre-o-sistema)
2. [Tecnologias Utilizadas](#2-tecnologias-utilizadas)
3. [Estrutura de Diretórios](#3-estrutura-de-diretórios)
4. [Pré-requisitos](#4-pré-requisitos)
5. [Instalação Rápida](#5-instalação-rápida)
6. [Instalação Detalhada](#6-instalação-detalhada)
7. [Uso do Sistema](#7-uso-do-sistema)
8. [Banco de Dados](#8-banco-de-dados)
9. [Testes Automatizados](#9-testes-automatizados)
10. [Manutenção](#10-manutenção)
11. [Solução de Problemas](#11-solução-de-problemas)
12. [Créditos](#12-créditos)

---

## 1. Sobre o Sistema

O **Sistema de Avaliação EN_430** é uma plataforma web para aplicação de avaliações online da disciplina **Introdução à Enfermagem**. Permite que estudantes:

- ✅ **Cadastrem-se** e criem uma conta pessoal
- ✅ **Façam login** e acessem o painel do estudante
- ✅ **Realizem avaliações** com questões aleatórias dos 10 módulos
- ✅ **Visualizem resultados** imediatos com gabarito detalhado
- ✅ **Acompanhem o progresso** por módulo e nível de dificuldade
- ✅ **Recuperem a senha** caso esqueçam (redefinição por pergunta de segurança)

### Funcionalidades Administrativas

- 👤 Cadastro/login de estudantes com senha criptografada (bcrypt)
- 📝 Avaliações com 20 questões aleatórias (configurável)
- 📊 Resultado detalhado com acertos/erros por questão
- 📈 Gráfico de progresso por módulo (Fácil, Médio, Difícil)
- 🛡️ Proteção CSRF contra ataques de formulário
- 🔒 Senhas com hash bcrypt (cost 12)
- 🗄️ Banco SQLite autocontido (sem instalação de banco externo)

---

## 2. Tecnologias Utilizadas

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

---

## 3. Estrutura de Diretórios

```
sistema_avaliacao/php/
│
├── index.php               # 🎯 Front controller (roteia todas as requisições)
├── config.php              # ⚙️ Constantes de configuração
├── db.php                  # 🗄️ Conexão PDO com SQLite
├── functions.php           # 🔧 Funções principais (auth, CSRF, helpers)
├── router.php              # 🚀 Roteador para PHP built-in server
├── .htaccess               # 📜 Regras de rewrite (Apache)
├── avaliacao.db            # 🗄️ Banco de dados SQLite (2.475 questões)
├── .gitignore              # 🔒 Arquivos ignorados pelo Git
├── phpunit.xml             # 🧪 Configuração do PHPUnit
├── composer.json           # 📦 Dependências (PHPUnit para teste)
├── composer.lock           # 📦 Lock das dependências
├── README.md               # 📋 Este arquivo
│
├── assets/                 # 🎨 Arquivos estáticos
│   ├── css/style.css       # Folha de estilos
│   └── js/app.js           # JavaScript do sistema
│
├── views/                  # 📄 Templates PHP
│   ├── index.php           # Página inicial (bem-vindo + módulos)
│   ├── cadastro.php        # Cadastro de estudante
│   ├── login.php           # Login do estudante
│   ├── painel.php          # Painel do estudante (dashboard)
│   ├── avaliacao.php       # Tela de avaliação (questões)
│   ├── resultado.php       # Resultado da avaliação + gabarito
│   ├── progresso.php       # Gráficos de progresso
│   ├── recuperar_acesso.php # Recuperação de senha
│   └── admin.php           # Painel administrativo
│
├── scripts/                # 🔧 Scripts de manutenção
│   ├── recriar_questoes.php     # Recria banco com 2.475 questões
│   └── questions_data.php       # Fonte de dados das questões
│
├── tests/                  # 🧪 Testes automatizados
│   ├── bootstrap.php       # Bootstrap para testes
│   ├── DatabaseTest.php    # Testes de banco de dados
│   ├── AuthTest.php        # Testes de autenticação
│   ├── AvaliacaoTest.php   # Testes de avaliação
│   └── IntegrationTest.php # Testes de integração HTTP
│
├── reports/                # 📊 Relatórios de code coverage (gerado)
└── vendor/                 # 📦 Dependências Composer (PHPUnit)
```

---

## 4. Pré-requisitos

| Requisito | Mínimo | Recomendado |
|-----------|--------|-------------|
| PHP | 8.1+ | 8.3+ |
| Extensões PHP | `pdo_sqlite`, `mbstring` | `pdo_sqlite`, `mbstring`, `session` |
| Servidor Web | Apache 2.4+ / Nginx | Apache 2.4+ com mod_rewrite |
| SQLite | 3.x (embutido no PHP) | — |
| Disco | 50 MB livres | 100 MB |
| RAM | 128 MB | 256 MB |

### Verificar pré-requisitos

```bash
# Verificar PHP
php -v

# Verificar extensões
php -m | grep -E 'pdo_sqlite|mbstring|session'

# Verificar servidor web (se Apache)
apache2 -v 2>/dev/null || httpd -v 2>/dev/null || echo "Servidor web não detectado"
```

---

## 5. Instalação Rápida

### Linux (Apache)

```bash
# 1. Copiar arquivos para o servidor
sudo mkdir -p /var/www/enfermagem/sistema_avaliacao
sudo cp -r php/ /var/www/enfermagem/sistema_avaliacao/

# 2. Instalar dependências PHP
sudo apt install -y php php-sqlite3 php-mbstring php-xml libapache2-mod-php

# 3. Ativar módulos Apache
sudo a2enmod rewrite headers expires deflate
sudo systemctl restart apache2

# 4. Configurar permissões
sudo chown -R www-data:www-data /var/www/enfermagem
sudo chmod -R 755 /var/www/enfermagem

# 5. Criar banco de dados
cd /var/www/enfermagem/sistema_avaliacao/php
php scripts/recriar_questoes.php
sudo chmod 664 avaliacao.db

# 6. Acessar: http://localhost/sistema_avaliacao/php/
```

### Windows (Apache)

```powershell
# 1. Copiar a pasta php/ para C:\Apache24\htdocs\en430\sistema_avaliacao\
# 2. Configurar PHP no Apache (ver GUIA_PUBLICACAO_APACHE_WINDOWS.md)
# 3. Abrir PowerShell como Administrador
cd C:\Apache24\htdocs\en430\sistema_avaliacao\php

# 4. Criar banco de dados
php scripts\recriar_questoes.php

# 5. Acessar: http://localhost/en430/sistema_avaliacao/php/
```

### Servidor PHP embutido (teste rápido)

```bash
cd sistema_avaliacao/php

# Se o banco não existir
php scripts/recriar_questoes.php

# Iniciar servidor
php -S 127.0.0.1:8080 -t . router.php

# Acessar: http://127.0.0.1:8080/
```

---

## 6. Instalação Detalhada

### 6.1. Linux — Apache + PHP 8

Siga o guia completo: [`GUIA_PUBLICACAO_APACHE.md`](../../GUIA_PUBLICACAO_APACHE.md)

### 6.2. Windows — Apache + PHP 8

Siga o guia completo: [`GUIA_PUBLICACAO_APACHE_WINDOWS.md`](../../GUIA_PUBLICACAO_APACHE_WINDOWS.md)

### 6.3. Scripts de instalação automatizada

| Sistema | Script | Como usar |
|---------|--------|-----------|
| 🐧 Linux | `../../instalar_publicar_linux.sh` | `sudo ./instalar_publicar_linux.sh` |
| 🪟 Windows | `../../instalar_publicar_windows.ps1` | PowerShell (Admin): `.\instalar_publicar_windows.ps1` |
| 🪟 Windows | `../../instalar_publicar_windows.bat` | Clique duas vezes no arquivo |

---

## 7. Uso do Sistema

### 7.1. Rotas disponíveis

| URL | Descrição |
|:---|:----------|
| `/` | Página inicial do sistema de avaliação |
| `/cadastro` | Cadastro de novo estudante |
| `/login` | Login do estudante |
| `/painel` | Painel do estudante (requer login) |
| `/avaliacao` | Iniciar nova avaliação |
| `/resultado` | Ver resultado da última avaliação |
| `/progresso` | Gráfico de progresso por módulo |
| `/recuperar-acesso` | Recuperação de senha |
| `/admin` | Painel administrativo |
| `/logout` | Sair da sessão |

### 7.2. Fluxo de uso

```
1. Cadastro → 2. Login → 3. Painel → 4. Nova Avaliação
                                         ↓
                                  Responder 20 questões
                                         ↓
                             5. Resultado + Gabarito
                                         ↓
                             6. Progresso (gráficos)
```

### 7.3. API interna

O sistema expõe endpoints JSON para funcionalidades AJAX:

| Endpoint | Método | Descrição |
|:---------|:-------|:----------|
| `api/questao?avaliacao=X&indice=Y` | GET | Retorna questão específica |
| `api/responder` | POST | Envia resposta do estudante |
| `api/finalizar` | POST | Finaliza a avaliação |

---

## 8. Banco de Dados

### 8.1. Esquema

O banco SQLite (`avaliacao.db`) contém as seguintes tabelas:

```sql
-- Tabela de estudantes
CREATE TABLE estudantes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    data_nascimento TEXT NOT NULL,
    telefone TEXT,
    email TEXT UNIQUE NOT NULL,
    senha_hash TEXT NOT NULL,           -- bcrypt cost 12
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de questões do banco
CREATE TABLE questoes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    modulo INTEGER NOT NULL,            -- 1 a 10
    dificuldade TEXT NOT NULL,          -- 'Fácil', 'Médio', 'Difícil'
    enunciado TEXT NOT NULL,
    opcao_a TEXT NOT NULL,
    opcao_b TEXT NOT NULL,
    opcao_c TEXT NOT NULL,
    opcao_d TEXT NOT NULL,
    resposta_correta TEXT NOT NULL      -- 'A', 'B', 'C' ou 'D'
);

-- Tabela de avaliações realizadas
CREATE TABLE avaliacoes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    estudante_id INTEGER NOT NULL,
    data_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_fim DATETIME,
    finalizada INTEGER DEFAULT 0,
    FOREIGN KEY (estudante_id) REFERENCES estudantes(id)
);

-- Tabela de respostas
CREATE TABLE respostas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    avaliacao_id INTEGER NOT NULL,
    questao_id INTEGER NOT NULL,
    resposta_estudante TEXT,
    correta INTEGER DEFAULT 0,
    FOREIGN KEY (avaliacao_id) REFERENCES avaliacoes(id),
    FOREIGN KEY (questao_id) REFERENCES questoes(id)
);
```

### 8.2. Distribuição das questões

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

### 8.3. Recriar o banco

⚠️ **Atenção:** Recriar o banco **APAGA todos os dados** de estudantes e avaliações!

```bash
cd sistema_avaliacao/php
php scripts/recriar_questoes.php
```

### 8.4. Backup do banco

```bash
# Linux
cp /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db \
   /var/backups/enfermagem/avaliacao_$(date +%Y%m%d).db

# Windows
copy C:\Apache24\htdocs\en430\sistema_avaliacao\php\avaliacao.db \
     C:\backups\en430\avaliacao_%DATE:~6,4%%DATE:~3,2%%DATE:~0,2%.db
```

---

## 9. Testes Automatizados

### 9.1. Instalar dependências de teste

```bash
cd sistema_avaliacao/php
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
php composer.phar install
```

### 9.2. Executar testes

```bash
# Todos os testes (sem code coverage)
php vendor/bin/phpunit --no-coverage

# Com code coverage (requer xdebug)
XDEBUG_MODE=coverage php -d zend_extension=./xdebug.so vendor/bin/phpunit --coverage-html=reports/

# Apenas testes específicos
php vendor/bin/phpunit --no-coverage tests/DatabaseTest.php
php vendor/bin/phpunit --no-coverage tests/AuthTest.php
php vendor/bin/phpunit --no-coverage tests/AvaliacaoTest.php
php vendor/bin/phpunit --no-coverage tests/IntegrationTest.php
```

### 9.3. Resultado esperado

```
PHPUnit 11.5.x

OK (61 tests, 199 assertions)
```

---

## 10. Manutenção

### 10.1. Atualizar código

```bash
cd /var/www/enfermagem/sistema_avaliacao/php

# Se usar Git
git pull

# OU copiar manualmente
cp -r /caminho/novo/php/* .

# Ajustar permissões
sudo chown -R www-data:www-data .
sudo chmod 664 avaliacao.db
```

### 10.2. Verificar logs

```bash
# Logs do Apache (Linux)
sudo tail -f /var/log/apache2/enfermagem_error.log
sudo tail -f /var/log/apache2/enfermagem_access.log

# Logs do Apache (Windows)
type C:\Apache24\logs\en430_error.log
```

### 10.3. Verificar integridade do banco

```bash
php -r "
\$db = new PDO('sqlite:avaliacao.db');
echo 'Integridade: ' . \$db->query('PRAGMA integrity_check')->fetchColumn() . PHP_EOL;
echo 'Questões: ' . \$db->query('SELECT COUNT(*) FROM questoes')->fetchColumn() . PHP_EOL;
echo 'Estudantes: ' . \$db->query('SELECT COUNT(*) FROM estudantes')->fetchColumn() . PHP_EOL;
echo 'Avaliações: ' . \$db->query('SELECT COUNT(*) FROM avaliacoes')->fetchColumn() . PHP_EOL;
"
```

---

## 11. Solução de Problemas

### ❌ "HTTP 500" — Página em branco

```bash
# 1. Verificar logs do Apache
# 2. Verificar permissão do banco
sudo chmod 664 /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db

# 3. Ativar exibição de erros (temporário — APENAS para debug)
# Crie um arquivo phpinfo.php na raiz do sistema:
# <?php phpinfo(); ?>
# E acesse http://localhost/sistema_avaliacao/php/phpinfo.php
# 
# OU edite o php.ini do Apache:
#   sudo nano /etc/php/8.x/apache2/php.ini
#   Altere: display_errors = On
#   Depois: sudo systemctl restart apache2
#
# OU adicione no início do index.php (temporário):
#   error_reporting(E_ALL);
#   ini_set('display_errors', 1);
```

### ❌ "No such file or directory" no banco

O caminho do banco SQLite está incorreto no `db.php`. Verifique:
```php
// db.php — a conexão usa caminho RELATIVO
new PDO('sqlite:' . __DIR__ . '/avaliacao.db');
```

### ❌ "Undefined array key" nos assets

O sistema espera que os assets estejam em `assets/css/` e `assets/js/`.  
Verifique se os arquivos existem e se o `.htaccess` está funcionando.

### ❌ "CSRF token mismatch"

Recarregue a página. O token CSRF expira após o logout ou timeout da sessão.

### ❌ PHPUnit não encontrado

```bash
cd sistema_avaliacao/php
php composer.phar install
```

---

## 12. Créditos

- **Disciplina:** Introdução à Enfermagem (EN_430)
- **Curso:** Técnico em Enfermagem — EAD/Subsequente
- **Ano:** 2026
- **Linguagem:** PHP 8
- **Banco:** SQLite
- **Total de questões:** 2.475
- **Guias de implantação:** Linux (`GUIA_PUBLICACAO_APACHE.md`) | Windows (`GUIA_PUBLICACAO_APACHE_WINDOWS.md`)

---

*Documentação gerada em Julho de 2026 para o Sistema de Avaliação EN_430.*  
*PHP 8 + Apache + SQLite • Material organizado para Estudos e Aprendizado 🎓*
