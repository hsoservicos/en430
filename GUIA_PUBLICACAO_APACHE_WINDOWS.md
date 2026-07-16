# 🪟 Guia de Publicação no Apache HTTP Server — Windows 10

> **Projeto:** Introdução à Enfermagem (EN_430) — Curso de Enfermagem EAD  
> **Conteúdo:** Central de Estudos + Sistema de Avaliação Web (PHP 8 + SQLite)  
> **SO do Servidor:** Windows 10  
> **Público-alvo:** Estudantes de Enfermagem  
> **Data de publicação:** Julho 2026

---

## 📋 Sumário

1. [Visão Geral da Arquitetura](#1-visão-geral-da-arquitetura)
2. [Pré-requisitos — Windows 10](#2-pré-requisitos--windows-10)
3. [Instalação do Apache no Windows](#3-instalação-do-apache-no-windows)
4. [Instalação do PHP 8 no Windows](#4-instalação-do-php-8-no-windows)
5. [Configuração do Apache com PHP](#5-configuração-do-apache-com-php)
6. [Estrutura de Diretórios no Servidor](#6-estrutura-de-diretórios-no-servidor)
7. [Configuração do VirtualHost (httpd.conf)](#7-configuração-do-virtualhost-httpdconf)
8. [Publicação dos Materiais Estáticos (HTML/PDF)](#8-publicação-dos-materiais-estáticos-htmlpdf)
9. [Publicação do Sistema de Avaliação (PHP + Apache)](#9-publicação-do-sistema-de-avaliação-php--apache)
10. [Segurança](#10-segurança)
11. [Manutenção e Backup](#11-manutenção-e-backup)
12. [Solução de Problemas — Windows](#12-solução-de-problemas--windows)

---

## 1. Visão Geral da Arquitetura

```
┌───────────────────────────────────────────────────────────────┐
│                    Windows 10 + Apache HTTP Server            │
│                                                               │
│  ┌─────────────────────────┐   ┌───────────────────────────┐  │
│  │   DocumentRoot          │   │  PHP 8 + Apache (mod_php) │  │
│  │   C:\Apache24\htdocs\   │   │                           │  │
│  │   en430\                │   │  C:\Apache24\htdocs\      │  │
│  │                         │   │    en430\                 │  │
│  │  • index.html           │   │    sistema_avaliacao\     │  │
│  │  • index_estudos.html   │   │    php\                   │  │
│  │  • *.html, *.pdf        │   │     ├── index.php         │  │
│  │                         │   │     ├── .htaccess         │  │
│  └─────────────────────────┘   │     ├── functions.php     │  │
│                                │     ├── db.php            │  │
│  Acesso: http://servidor/      │     ├── config.php        │  │
│  en430/                        │     ├── avaliacao.db      │  │
│                                │     ├── assets/           │  │
│  Acesso: http://servidor/      │     └── views/            │  │
│  en430/php/                    └───────────────────────────┘  │
│                                                               │
└───────────────────────────────────────────────────────────────┘
```

### ⚠️ Diferenças do Guia Linux

| Aspecto | Linux | Windows 10 |
|---------|-------|------------|
| **Apache** | `apt install apache2` | Apache Lounge (zip manual) |
| **PHP** | `apt install php php-sqlite3` | PHP para Windows (zip manual) |
| **mod_php** | `libapache2-mod-php` | `LoadModule php_module` manual |
| **Gerenciamento** | `systemctl` | `httpd -k install` + `net start` |
| **VirtualHost** | Arquivos separados em `sites-available/` | Tudo no `httpd.conf` |
| **Módulos** | `a2enmod` | Editar manualmente o `httpd.conf` |
| **Permissões** | `chown www-data` | Permissões NTFS do Windows |
| **Backup** | `crontab` | Agendador de Tarefas do Windows |
| **HTTPS** | Certbot (Let's Encrypt) | Certbot ou certificado manual |

---

## 2. Pré-requisitos — Windows 10

### 2.1. Requisitos de Sistema

| Componente | Mínimo | Recomendado |
|------------|--------|-------------|
| SO | Windows 10 64-bit | Windows 10 22H2+ |
| RAM | 1 GB | 2 GB+ |
| Disco | 500 MB livres | 1 GB |
| PHP | 8.1+ (64-bit) | 8.3+ (64-bit) |
| Apache | 2.4.x (64-bit) | 2.4.62+ |
| SQLite | 3.x (embutido no PHP) | — |

### 2.2. Download dos instaladores

| Software | Onde baixar | Observação |
|----------|------------|------------|
| 🖥️ **Apache 2.4 (64-bit)** | [Apache Lounge](https://www.apachelounge.com/download/) | Use a versão **64-bit** — extraia para `C:\Apache24` |
| 🐘 **PHP 8.3+ (64-bit)** | [windows.php.net](https://windows.php.net/download/) | Baixe o **zip** `VS16 x64 Non Thread Safe` ou `Thread Safe` |
| 🛠️ **Visual C++ Redistributable** | [Microsoft](https://aka.ms/vs/17/release/vc_redist.x64.exe) | Necessário para Apache e PHP |

> ⚠️ **Arquitetura:** Todos os componentes DEVEM ser 64-bit. Misturar 32-bit com 64-bit causa erro na inicialização do Apache.

---

## 3. Instalação do Apache no Windows

### Opção A — Apache Lounge (recomendado para produção)

```cmd
:: 1. Baixe o Apache 2.4.62 (ou superior) 64-bit do Apache Lounge
:: 2. Extraia o conteúdo para C:\Apache24\

:: Estrutura esperada após extrair:
:: C:\Apache24\
::   ├── bin\          (httpd.exe, etc.)
::   ├── conf\         (httpd.conf)
::   ├── htdocs\       (DocumentRoot padrão)
::   ├── modules\      (módulos .so)
::   └── logs\         (logs de erro/access)
```

### Opção B — XAMPP (mais simples para testes)

```cmd
:: 1. Baixe o XAMPP em: https://www.apachefriends.org/
:: 2. Execute o instalador — instale para C:\xampp\
:: 3. O Apache estará em C:\xampp\apache\ e o PHP em C:\xampp\php\
:: 4. O htdocs em C:\xampp\htdocs\

:: Para iniciar/parar o Apache no XAMPP:
::   • Use o XAMPP Control Panel (interface gráfica)
::   • Ou via linha de comando:
C:\xampp\apache\bin\httpd.exe -k start
C:\xampp\apache\bin\httpd.exe -k stop
```

### 3.1. Configurar o nome do servidor

Edite `C:\Apache24\conf\httpd.conf` e descomente/altere:

```apache
# Linha ~39: Defina o ServerName
ServerName localhost:80

# Linha ~60: Ajuste o DocumentRoot (opcional)
DocumentRoot "C:/Apache24/htdocs"
<Directory "C:/Apache24/htdocs">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

> 🔑 **Importante:** Use **sempre barras normais (`/`)** nos caminhos do Apache no Windows.  
> Barras invertidas (`\`) podem causar erros de sintaxe no `httpd.conf`.

### 3.2. Testar e instalar como serviço

```cmd
:: Abra o Prompt como Administrador

:: Testar a configuração
C:\Apache24\bin\httpd.exe -t

:: Se retornar "Syntax OK" — prossiga
:: Se houver erro, corrija antes de continuar

:: Instalar como serviço do Windows
C:\Apache24\bin\httpd.exe -k install

:: Iniciar o serviço
C:\Apache24\bin\httpd.exe -k start

:: Ou usar comandos do Windows:
:: net start Apache2.4
:: net stop Apache2.4
```

### 3.3. Liberar porta no Firewall do Windows

```cmd
:: Executar como Administrador
netsh advfirewall firewall add rule name="Apache HTTP (80)" dir=in action=allow protocol=TCP localport=80

:: Se for usar HTTPS (443):
netsh advfirewall firewall add rule name="Apache HTTPS (443)" dir=in action=allow protocol=TCP localport=443

:: Verificar regras criadas
netsh advfirewall firewall show rule name="Apache HTTP (80)"
```

### 3.4. Verificar a instalação

Abra o navegador e acesse: **http://localhost/**

Você deve ver a página padrão do Apache ("It works!").

> 💡 Para acessar de outros computadores na rede, use o IP do servidor:
> `http://192.168.x.x/` (substitua pelo IP real da máquina)

---

## 4. Instalação do PHP 8 no Windows

> 💡 **XAMPP:** Se você está usando **XAMPP**, pule esta seção — o PHP já vem instalado junto com o Apache. Vá direto para a [Seção 6](#6-estrutura-de-diretórios-no-servidor).

### 4.1. Baixar e extrair o PHP

```cmd
:: 1. Baixe o PHP 8.3+ (64-bit) de https://windows.php.net/download/
::    Escolha a versão "VS16 x64 Thread Safe" para usar com Apache mod_php

:: 2. Extraia o conteúdo para C:\php

:: Estrutura esperada:
:: C:\php\
::   ├── php.exe          (CLI)
::   ├── php8ts.dll       (Thread Safe DLL)
::   ├── php.ini-development
::   ├── ext\             (Extensões)
::   └── ...
```

### 4.2. Configurar o php.ini

```cmd
:: Copiar o php.ini-development como php.ini
copy C:\php\php.ini-development C:\php\php.ini

:: Editar C:\php\php.ini com Bloco de Notas
:: Descomente (remova o ;) as seguintes extensões essenciais:
extension=php_pdo_sqlite.dll
extension=php_sqlite3.dll
extension=php_mbstring.dll
extension=php_openssl.dll
extension=php_curl.dll

:: Habilite o log de erros (importante para debug):
error_log = "C:/php/logs/php_errors.log"
log_errors = On
```

### 4.3. Verificar a instalação do PHP

```cmd
:: Testar se o PHP funciona
C:\php\php.exe -v
:: Deve mostrar: PHP 8.3.x (cli)

:: Verificar extensões carregadas
C:\php\php.exe -m
:: Deve mostrar: pdo_sqlite, sqlite3, mbstring, openssl
```

### 4.4. Adicionar PHP ao PATH do Windows

```cmd
:: Painel de Controle → Sistema → Configurações Avançadas
:: → Variáveis de Ambiente → Path → Editar → Novo
:: Adicione: C:\php

:: Após adicionar, reinicie o Prompt e teste:
php -v
```

---

## 5. Configuração do Apache com PHP

### 5.1. Carregar o módulo PHP no Apache

Edite `C:\Apache24\conf\httpd.conf` e adicione **no final da seção de módulos** (após os `LoadModule` existentes):

```apache
# ─── PHP 8 ────────────────────────────────────────────────────────
LoadModule php_module "C:/php/php8apache2_4.dll"
<FilesMatch \.php$>
    SetHandler application/x-httpd-php
</FilesMatch>

# Caminho para o php.ini
PHPIniDir "C:/php"
```

> 🔑 Para **XAMPP**, o PHP já está configurado. Pule para a seção 6.

### 5.2. Adicionar index.php como página padrão

Localize a linha `DirectoryIndex` no `httpd.conf` e adicione o `index.php`:

```apache
DirectoryIndex index.php index.html index_estudos.html
```

### 5.3. Ativar módulos necessários

Descomente (remova o `#`) no `httpd.conf` as seguintes linhas:

```apache
LoadModule expires_module modules/mod_expires.so      # Cache
LoadModule deflate_module modules/mod_deflate.so      # Compressão
LoadModule headers_module modules/mod_headers.so      # Headers de segurança
LoadModule rewrite_module modules/mod_rewrite.so      # URL amigáveis
```

### 5.4. Testar o PHP no Apache

```cmd
:: 1. Criar um arquivo de teste com Bloco de Notas:
notepad C:\Apache24\htdocs\info.php
:: Digite: <?php phpinfo(); ?>
:: Salve e feche

:: 2. Reiniciar o Apache
C:\Apache24\bin\httpd.exe -k restart

:: 3. Testar no navegador
:: http://localhost/info.php
:: Deve mostrar a página de informações do PHP com suporte a SQLite

:: 4. Remover o arquivo de teste após verificar
del C:\Apache24\htdocs\info.php
```

---

## 6. Estrutura de Diretórios no Servidor

```
C:\xampp\htdocs\en430\                   # DocumentRoot para o projeto
│
├── index.html                            # 🏠 Landing page (Apache padrão)
├── index_estudos.html                    # 🗺️ Central de Estudos
├── guia_completo_estudos.html            # 📖 Guia de Estudos
├── Guia_Completo_Estudos_Introducao_Enfermagem.pdf
├── ... (demais HTMLs e PDFs dos artefatos)
│
├── Apostila_Completa_Introducao_Enfermagem.pdf  # 📚 Compilado completo
├── GUIA_PUBLICACAO_APACHE_WINDOWS.md     # 📋 Este documento
│
└── sistema_avaliacao\                    # 🌐 Sistema de Avaliação PHP
    └── php\                              # Código PHP do sistema
        ├── index.php                     # Front controller (roteador)
        ├── config.php                    # Configurações do sistema
        ├── db.php                        # Conexão PDO SQLite
        ├── functions.php                 # Funções (auth, CSRF, helpers)
        ├── router.php                    # Roteador para PHP built-in server
        ├── .htaccess                     # Regras de rewrite para Apache
        ├── avaliacao.db                  # Banco SQLite (2475 questões)
        ├── README.md                     # Documentação do sistema
        ├── assets\
        │   ├── css\style.css             # Folha de estilos
        │   └── js\app.js                 # JavaScript do sistema
        ├── views\                        # Templates PHP
        │   ├── index.php
        │   ├── cadastro.php
        │   ├── login.php
        │   ├── painel.php
        │   ├── avaliacao.php
        │   ├── resultado.php
        │   ├── progresso.php
        │   ├── recuperar_acesso.php
        │   └── admin.php
        ├── scripts\                      # Scripts de manutenção
        │   ├── recriar_questoes.php      # Recria banco (+2400 questões)
        │   └── questions_data.php        # Banco de questões fonte
        └── tests\                        # Testes PHPUnit
            ├── bootstrap.php
            ├── DatabaseTest.php
            ├── AuthTest.php
            ├── AvaliacaoTest.php
            └── IntegrationTest.php
```

### Transferir arquivos para o servidor

```cmd
:: Opção 1: Remoto via SCP (se tiver OpenSSH no Windows)
scp -r C:\caminho\local\* usuario@servidor:C:\Apache24\htdocs\en430\

:: Opção 2: Unidade de rede
copy C:\caminho\local\* Z:\Apache24\htdocs\en430\

:: Opção 3: Pendrive / compartilhamento de arquivos
:: Copie manualmente os arquivos para C:\Apache24\htdocs\en430\
```

---

## 7. Configuração do VirtualHost (httpd.conf)

Edite o arquivo `C:\Apache24\conf\httpd.conf` com um editor de texto (Bloco de Notas, VS Code, Notepad++).

Adicione **no final do arquivo** (antes do `</IfDefine>` final, se houver):

```apache
<VirtualHost *:80>
    ServerName en430.localhost
    DocumentRoot "C:/Apache24/htdocs/en430"

    DirectoryIndex index.php index.html index_estudos.html

    <Directory "C:/Apache24/htdocs/en430">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # ─── Sistema de Avaliação (PHP) ──────────────────────
    # O .htaccess dentro de sistema_avaliacao/php/ cuida do roteamento
    <Directory "C:/Apache24/htdocs/en430/sistema_avaliacao/php">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # ─── Cache de Arquivos Estáticos ─────────────────────
    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/html "access plus 1 day"
        ExpiresByType application/pdf "access plus 7 days"
        ExpiresByType text/css "access plus 7 days"
        ExpiresByType application/javascript "access plus 7 days"
        ExpiresByType image/png "access plus 30 days"
        ExpiresByType image/jpeg "access plus 30 days"
    </IfModule>

    # ─── Compressão ──────────────────────────────────────
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/css application/javascript
        AddOutputFilterByType DEFLATE application/pdf text/plain
    </IfModule>

    # ─── Cabeçalhos de Segurança ─────────────────────────
    <IfModule mod_headers.c>
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-Frame-Options "DENY"
        Header always set X-XSS-Protection "1; mode=block"
        Header always set Referrer-Policy "strict-origin-when-cross-origin"
    </IfModule>

    # ─── Logs ────────────────────────────────────────────
    ErrorLog "C:/Apache24/logs/en430_error.log"
    CustomLog "C:/Apache24/logs/en430_access.log" combined
</VirtualHost>
```

**Para XAMPP**, ajuste os caminhos:

```apache
<VirtualHost *:80>
    ServerName en430.localhost
    DocumentRoot "C:/xampp/htdocs/en430"
    ...
    ErrorLog "C:/xampp/apache/logs/en430_error.log"
    CustomLog "C:/xampp/apache/logs/en430_access.log" combined
</VirtualHost>
```

### 7.1. Testar e reiniciar

```cmd
:: Testar a configuração (como Administrador)
C:\Apache24\bin\httpd.exe -t

:: Se retornar "Syntax OK" — reinicie o Apache
C:\Apache24\bin\httpd.exe -k restart

:: Verificar logs se houver erro
type C:\Apache24\logs\error.log | more
```

---

## 8. Publicação dos Materiais Estáticos (HTML/PDF)

Os materiais de estudo (HTMLs e PDFs) são servidos diretamente pelo Apache a partir do `DocumentRoot`.

### 8.1. Copiar os arquivos

```cmd
:: Criar diretório do projeto
mkdir C:\Apache24\htdocs\en430

:: Copiar todos os arquivos HTML e PDF
copy C:\caminho\origem\*.html C:\Apache24\htdocs\en430\
copy C:\caminho\origem\*.pdf  C:\Apache24\htdocs\en430\
copy C:\caminho\origem\*.txt  C:\Apache24\htdocs\en430\
copy C:\caminho\origem\*.md   C:\Apache24\htdocs\en430\

:: Copiar o sistema de avaliação PHP
xcopy C:\caminho\origem\sistema_avaliacao C:\Apache24\htdocs\en430\sistema_avaliacao\ /E /I
```

### 8.2. Verificar permissões NTFS

```cmd
:: O serviço Apache roda como "Local System" ou "Network Service"
:: Certifique-se de que estas contas têm acesso de leitura/gravação:

icacls C:\Apache24\htdocs\en430 /grant "BUILTIN\Users:(OI)(CI)RX" /T

:: O banco SQLite precisa de permissão de escrita:
icacls C:\Apache24\htdocs\en430\sistema_avaliacao\php\avaliacao.db /grant "BUILTIN\Users:(RW)"
```

---

## 9. Publicação do Sistema de Avaliação (PHP + Apache)

### 9.1. Como funciona o roteamento PHP

O Apache, através do módulo `mod_rewrite` e do arquivo `.htaccess`, redireciona todas as requisições para o front controller `index.php`, que interpreta a URL e carrega a view correta.

Fluxo:
```
Requisição → Apache → .htaccess (rewrite) → index.php (front controller) → View PHP → Resposta HTML
```

### 9.2. O arquivo .htaccess

O sistema já inclui um `.htaccess` em `sistema_avaliacao/php/.htaccess` com as regras necessárias:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirecionar para index.php se não for arquivo/diretório real
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>

# Bloquear acesso a arquivos sensíveis
<FilesMatch "\.(db|sqlite|md|txt|log|ini)$">
    Require all denied
</FilesMatch>
```

> 💡 O `.htaccess` funciona no Apache para Windows desde que o `AllowOverride All` esteja configurado no VirtualHost (seção 7).

### 9.3. Recriar o banco de dados

```cmd
:: No diretório PHP do sistema:
cd C:\Apache24\htdocs\en430\sistema_avaliacao\php

:: Recriar o banco com todas as tabelas e questões
php scripts\recriar_questoes.php

:: Verificar o banco de dados
php -r "$db = new SQLite3('avaliacao.db'); echo $db->querySingle('SELECT COUNT(*) FROM questoes') . ' questões\n';"
:: Deve retornar: 2475 questões
```

### 9.4. Testar o sistema de avaliação

```cmd
:: Com o Apache rodando, teste no navegador:
:: http://localhost/en430/sistema_avaliacao/php/
:: Deve mostrar a página inicial do sistema de avaliação

:: Teste com curl (se tiver instalado):
curl -s -o /dev/null -w "%{http_code}" http://localhost/en430/sistema_avaliacao/php/
:: Deve retornar 200

:: Testar rotas internas:
curl -s -o /dev/null -w "%{http_code}" http://localhost/en430/sistema_avaliacao/php/cadastro
curl -s -o /dev/null -w "%{http_code}" http://localhost/en430/sistema_avaliacao/php/login
curl -s -o /dev/null -w "%{http_code}" http://localhost/en430/sistema_avaliacao/php/recuperar-acesso
```

### 9.5. Teste com servidor PHP embutido (alternativa)

Se o Apache não estiver disponível, o sistema pode ser executado com o servidor PHP embutido:

```cmd
cd C:\Apache24\htdocs\en430\sistema_avaliacao\php
php -S 127.0.0.1:8080 -t . router.php
```

Acesse: **http://127.0.0.1:8080/**

---

## 10. Segurança

### 10.1. Proteger arquivos sensíveis

No `httpd.conf`, dentro da seção `<VirtualHost>`, adicione:

```apache
# Proteger arquivos sensíveis
<FilesMatch "\.(db|sqlite|md|txt|log|ini)$">
    Require all denied
</FilesMatch>
```

### 10.2. .htaccess na raiz do projeto

Crie `C:\Apache24\htdocs\en430\.htaccess`:

```apache
# Proteger arquivos sensíveis
<FilesMatch "\.(db|sqlite|md|txt|log|ini)$">
    Require all denied
</FilesMatch>

# URLs amigáveis para os materiais
RewriteEngine On
RewriteRule ^estudos$ index_estudos.html [L]
```

### 10.3. HTTPS no Windows

**Opção 1 — Certbot (recomendado para produção):**
```cmd
:: Baixe o Certbot para Windows em: https://certbot.eff.org/
certbot --apache -d enfermagem.seu-dominio.com
```

**Opção 2 — Certificado auto-assinado (para testes):**
```cmd
:: Use o PowerShell para gerar um certificado auto-assinado
:: ou utilize ferramentas como OpenSSL
```

### 10.4. Senhas no banco de dados

As senhas dos estudantes são armazenadas com **bcrypt** (cost 12):
> ✅ Senhas nunca armazenadas em texto plano  
> ✅ Salt automático para cada senha  
> ✅ Algoritmo bcrypt com cost 12 (OWASP recomendado)

---

## 11. Manutenção e Backup

### 11.1. Backup do banco de dados (Agendador de Tarefas)

```cmd
:: Criar script de backup: C:\scripts\backup_avaliacao.bat
@echo off
set DATA=%DATE:~6,4%%DATE:~3,2%%DATE:~0,2%
copy "C:\Apache24\htdocs\en430\sistema_avaliacao\php\avaliacao.db"
     "C:\backups\en430\avaliacao-%DATA%.db"
echo Backup concluido: %DATA%
```

**Configurar no Agendador de Tarefas:**
1. Abra o **Agendador de Tarefas** (taskschd.msc)
2. Criar Tarefa Básica → "Backup Avaliação"
3. Disparador: **Diariamente** às **02:00**
4. Ação: **Iniciar programa** → `C:\scripts\backup_avaliacao.bat`

### 11.2. Atualizar materiais

```cmd
:: Parar o Apache
C:\Apache24\bin\httpd.exe -k stop

:: Copiar novos arquivos
copy C:\novos_materiais\* C:\Apache24\htdocs\en430\

:: Reiniciar o Apache
C:\Apache24\bin\httpd.exe -k start
```

### 11.3. Atualizar sistema de avaliação

```cmd
:: Parar o Apache
C:\Apache24\bin\httpd.exe -k stop

:: Atualizar código
cd C:\Apache24\htdocs\en430\sistema_avaliacao\php
git pull  # ou copiar manualmente

:: Recriar banco se necessário (remove dados existentes)
php scripts\recriar_questoes.php

:: Ajustar permissões
icacls avaliacao.db /grant "BUILTIN\Users:(RW)"

:: Reiniciar o Apache
C:\Apache24\bin\httpd.exe -k start
```

### 11.4. Monitoramento básico

```cmd
:: Verificar status do Apache (Windows)
net start Apache2.4
:: ou: sc query Apache2.4 | findstr STATE

:: Verificar logs de erro
type C:\Apache24\logs\en430_error.log

:: Monitorar logs em tempo real (PowerShell)
Get-Content C:\Apache24\logs\en430_access.log -Wait

:: Testar resposta do sistema
curl -s -o /dev/null -w "%{http_code}" http://localhost/en430/sistema_avaliacao/php/
```

---

## 12. Solução de Problemas — Windows

### ❌ Erro: "httpd.exe não é um aplicativo Win32 válido"

**Causa:** Misturou versão 32-bit com 64-bit.  
**Solução:** Todos os componentes DEVEM ser 64-bit:
- Apache Lounge 64-bit
- PHP 64-bit
- Visual C++ Redistributable 64-bit

### ❌ Erro: ".ps1 não pode ser carregado porque a execução de scripts foi desabilitada"

**Causa:** A política de execução do PowerShell bloqueia scripts.

**Solução:**

**Opção 1 — Clique duas vezes no arquivo `.bat`** (mais simples):
```cmd
:: Na pasta do projeto existe o arquivo:
instalar_publicar_windows.bat
:: Basta clicar duas vezes nele — executa sem travar na política.
```

**Opção 2 — PowerShell com política temporária:**
```powershell
powershell -ExecutionPolicy RemoteSigned -File .\instalar_publicar_windows.ps1
```

**Opção 3 — Permitir apenas para esta sessão:**
```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy RemoteSigned
.\instalar_publicar_windows.ps1
```

### ❌ Erro: "Cannot load php8apache2_4.dll" ou módulo não encontrado

**Causa:** O caminho do `LoadModule php_module` está incorreto ou o PHP não é Thread Safe.

**Solução:**
```cmd
:: Verificar se o arquivo php8apache2_4.dll existe
dir C:\php\php8apache2_4.dll

:: Se não existir, baixe a versão Thread Safe do PHP
:: A versão "Non Thread Safe" NÃO funciona com Apache mod_php

:: Verificar dependências DLL (Visual C++ Redistributable)
:: Instale o Visual C++ Redistributable 64-bit
```

### ❌ Erro: "httpd: Syntax error on line ..."

**Causa:** Erro de sintaxe no `httpd.conf`.  
**Solução:**
```cmd
:: Use sempre barras NORMAIS (/) nos caminhos
:: Verifique aspas duplas ao redor de caminhos com espaços
:: Verifique se todos os módulos referenciados existem em modules/
```

### ❌ Erro 500 — Internal Server Error no PHP

**Causa:** Erro na aplicação PHP ou permissão no banco SQLite.

**Solução:**
```cmd
:: 1. Verificar logs de erro do Apache
type C:\Apache24\logs\en430_error.log

:: 2. Verificar permissão no banco SQLite
icacls C:\Apache24\htdocs\en430\sistema_avaliacao\php\avaliacao.db /grant "BUILTIN\Users:(RW)"

:: 3. Habilitar exibição de erros no php.ini
:: display_errors = On
:: Reiniciar o Apache após alterar

:: 4. Verificar sintaxe do arquivo PHP
php -l C:\Apache24\htdocs\en430\sistema_avaliacao\php\index.php
```

### ❌ Erro: "Access Denied" no banco SQLite

**Causa:** O serviço do Apache não tem permissão de escrita no arquivo `.db`.  
**Solução:**
```cmd
:: Dar permissão explícita para o banco
icacls C:\Apache24\htdocs\en430\sistema_avaliacao\php\avaliacao.db /grant "BUILTIN\Users:(RW)"

:: Ou alterar o serviço para rodar como sua conta de usuário
:: services.msc → Apache2.4 → Log On → "This account" → sua conta
```

### ❌ Erro 403 — Forbidden

**Causa:** Permissões de diretório no Apache.  
**Solução:**
```cmd
:: Verificar a diretiva <Directory> no httpd.conf
:: Deve conter: Require all granted

:: Verificar permissões NTFS
icacls C:\Apache24\htdocs\en430
```

### ❌ Apache não inicia após configurar PHP

**Causa:** DLL ausente ou conflito de versões (32-bit × 64-bit).  
**Solução:**
```cmd
:: Verificar a saída completa do erro
C:\Apache24\bin\httpd.exe -t 2>&1

:: Se mencionar "VCRUNTIME140.dll", instale o Visual C++ Redistributable:
:: https://aka.ms/vs/17/release/vc_redist.x64.exe

:: Se mencionar "php8apache2_4.dll", verifique se é a versão Thread Safe
:: Baixe a versão correta em: https://windows.php.net/download/
```

### ❌ Sistema de avaliação mostra página sem estilo (CSS/JS)

**Causa:** Caminhos relativos incorretos ou `.htaccess` não processado.

**Solução:**
```cmd
:: 1. Verificar se o AllowOverride está ativado no VirtualHost
::    Deve conter: AllowOverride All

:: 2. Verificar se o mod_rewrite está ativado
::    No httpd.conf: LoadModule rewrite_module modules/mod_rewrite.so

:: 3. Abrir o console do navegador (F12) para ver erros 404 nos assets

:: 4. Verificar a função url() no functions.php — deve gerar caminhos corretos
```

---

## 📄 Referências

- [Apache Lounge (Apache para Windows)](https://www.apachelounge.com/)
- [PHP para Windows](https://windows.php.net/download/)
- [Documentação PHP — SQLite (PDO)](https://www.php.net/manual/pt_BR/ref.pdo-sqlite.php)
- [Visual C++ Redistributable](https://aka.ms/vs/17/release/vc_redist.x64.exe)
- [Documentação do Sistema de Avaliação](sistema_avaliacao/README.md)
- [Apache HTTP Server Documentation](https://httpd.apache.org/docs/)

---

## 🐘 Comandos Rápidos (Para Consulta)

```cmd
:: ─── APACHE ───────────────────────────────────────────
C:\Apache24\bin\httpd.exe -t              :: Testar configuração
C:\Apache24\bin\httpd.exe -k restart       :: Reiniciar Apache
C:\Apache24\bin\httpd.exe -k stop          :: Parar Apache
C:\Apache24\bin\httpd.exe -k start         :: Iniciar Apache

:: ─── PHP ──────────────────────────────────────────────
php -v                                      :: Versão do PHP
php -m                                      :: Módulos carregados
php -l arquivo.php                          :: Validar sintaxe
php scripts\recriar_questoes.php            :: Recriar banco
php -S 127.0.0.1:8080 -t . router.php       :: Servidor embutido
php vendor\bin\phpunit                       :: Rodar testes

:: ─── SISTEMA ──────────────────────────────────────────
:: http://localhost/en430/sistema_avaliacao/php/
:: http://localhost/en430/sistema_avaliacao/php/cadastro
:: http://localhost/en430/sistema_avaliacao/php/login
:: http://localhost/en430/sistema_avaliacao/php/painel
:: http://localhost/en430/sistema_avaliacao/php/recuperar-acesso
:: http://localhost/en430/sistema_avaliacao/php/admin
```

---

*Guia de publicação para Windows 10 elaborado para a disciplina **Introdução à Enfermagem (EN_430)***  
*Curso de Enfermagem — EAD/Subsequente • Julho 2026*  
*PHP 8 + Apache + SQLite • Reformulado a partir do original Python/Flask*  
*Material organizado para **Estudos e Aprendizado** 🎓*

---

### 📋 Documentos Relacionados

- [`checklist_verificacao.md`](checklist_verificacao.md) — Checklist de verificação pós-deploy
- [`GUIA_PUBLICACAO_APACHE.md`](GUIA_PUBLICACAO_APACHE.md) — Guia para servidores Linux (Ubuntu/Debian)
- [`sistema_avaliacao/php/README.md`](sistema_avaliacao/php/README.md) — Documentação do sistema PHP
