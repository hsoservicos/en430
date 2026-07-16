# 🐧 Guia de Publicação no Apache HTTP Server — Linux (Ubuntu/Debian)

> **Projeto:** Introdução à Enfermagem (EN_430) — Curso de Enfermagem EAD  
> **Conteúdo:** Central de Estudos + Sistema de Avaliação Web (PHP 8 + SQLite)  
> **SO do Servidor:** Ubuntu 20.04+ / Debian 11+  
> **Público-alvo:** Estudantes de Enfermagem  
> **Data de publicação:** Julho 2026

---

## 📋 Sumário

1. [Visão Geral da Arquitetura](#1-visão-geral-da-arquitetura)
2. [Pré-requisitos](#2-pré-requisitos)
3. [Instalação Passo a Passo (para iniciantes)](#3-instalação-passo-a-passo-para-iniciantes)
4. [Estrutura de Diretórios no Servidor](#4-estrutura-de-diretórios-no-servidor)
5. [Configuração Manual do Apache](#5-configuração-manual-do-apache)
6. [Publicação dos Materiais Estáticos (HTML/PDF)](#6-publicação-dos-materiais-estáticos-htmlpdf)
7. [Publicação do Sistema de Avaliação (PHP + Apache)](#7-publicação-do-sistema-de-avaliação-php--apache)
8. [VirtualHost Completo — Exemplo](#8-virtualhost-completo--exemplo)
9. [Segurança](#9-segurança)
10. [Manutenção e Backup](#10-manutenção-e-backup)
11. [Solução de Problemas](#11-solução-de-problemas)
12. [Checklist Pós-Deploy](#12-checklist-pós-deploy)
13. [Comandos Rápidos](#13-comandos-rápidos)

---

## 1. Visão Geral da Arquitetura

```
┌───────────────────────────────────────────────────────────────┐
│              Ubuntu/Debian + Apache HTTP Server                │
│                                                               │
│  ┌─────────────────────────┐   ┌───────────────────────────┐  │
│  │   DocumentRoot           │   │  PHP 8 + Apache (mod_php) │  │
│  │   /var/www/enfermagem/   │   │                           │  │
│  │                          │   │  /var/www/enfermagem/     │  │
│  │  • index.html            │   │  sistema_avaliacao/      │  │
│  │  • index_estudos.html    │   │  php/                    │  │
│  │  • *.html, *.pdf         │   │   ├── index.php          │  │
│  │                          │   │   ├── .htaccess          │  │
│  └─────────────────────────┘   │   ├── functions.php       │  │
│                                │   ├── db.php              │  │
│  Acesso: http://servidor/      │   ├── config.php          │  │
│  enfermagem/                   │   ├── avaliacao.db        │  │
│                                │   ├── assets/             │  │
│  Acesso: http://servidor/      │   └── views/              │  │
│  enfermagem/sistema_avaliacao/ └───────────────────────────┘  │
│  php/                                                         │
└───────────────────────────────────────────────────────────────┘
```

### 🧩 Como os componentes se comunicam

| Componente | Tecnologia | Como é servido |
|------------|-----------|----------------|
| 📄 Landing page | `index.html` | Apache direto (DocumentRoot) |
| 🗺️ Central de Estudos | `index_estudos.html` | Apache direto |
| 📚 Apostilas, Guias, PDFs | Arquivos `.html` e `.pdf` | Apache direto |
| 🌐 **Sistema de Avaliação** | **PHP 8 + SQLite** | **Apache + mod_php** |
| 🗄️ Banco de Dados | SQLite (`avaliacao.db`) | PDO pelo PHP (Apache lê/escreve) |

### 🔄 Fluxo de uma requisição para o Sistema de Avaliação

```
Usuário no navegador
       │
       ▼
http://servidor/enfermagem/sistema_avaliacao/php/login
       │
       ▼
Apache HTTP Server (porta 80)
       │
       ▼
.htaccess (mod_rewrite) — Se o arquivo não existe fisicamente,
redireciona para index.php
       │
       ▼
index.php (Front Controller) — Analisa a URL e decide o que fazer
       │
       ├── Rota "login" → Carrega views/login.php
       ├── Rota "cadastro" → Carrega views/cadastro.php
       ├── Rota "avaliacao" → Carrega views/avaliacao.php
       └── Rota "api/*" → Executa função e retorna JSON
       │
       ▼
Resposta HTML/JSON → Navegador do usuário
```

---

## 2. Pré-requisitos

### 2.1. Servidor — Requisitos Mínimos

| Componente | Mínimo | Recomendado |
|------------|--------|-------------|
| SO | Ubuntu 20.04+ / Debian 11+ | Ubuntu 22.04 LTS ou 24.04 LTS |
| RAM | 512 MB | 1 GB+ |
| Disco | 100 MB livres | 500 MB |
| PHP | 8.1+ | 8.3+ |
| Apache | 2.4+ | 2.4.58+ |

### 2.2. Conhecimentos necessários

> 💡 **Para iniciantes:** Você só precisa saber:
> - O que é um terminal (linha de comando)
> - Ter acesso ao servidor com usuário `root` ou com `sudo`
> - Saber copiar/colar comandos
>
> 📋 **Ao final da instalação, use o checklist:** [`checklist_verificacao.md`](checklist_verificacao.md)
> para verificar se tudo está funcionando corretamente.

### 2.3. Pacotes que serão instalados

| Pacote | Função |
|--------|--------|
| `apache2` | Servidor web HTTP |
| `php` | Interpretador PHP 8 |
| `php-sqlite3` | Suporte a banco SQLite no PHP |
| `php-mbstring` | Suporte a caracteres especiais (acentos) |
| `php-xml` | Suporte a XML |
| `libapache2-mod-php` | Integração do PHP com Apache |
| `sqlite3` | Cliente de linha de comando do SQLite |
| `curl` | Cliente HTTP (usado nos testes) |

---

## 3. Instalação Passo a Passo (para iniciantes)

> ⚡ **Método automático:** Use o script `instalar_publicar_linux.sh` (seção 3.1)  
> 📖 **Manual:** Siga os passos abaixo (seção 3.2 em diante)

### 3.1. Método Automático (Recomendado)

```bash
# 1. Entre na pasta do projeto
cd /caminho/para/o/projeto

# 2. Dê permissão de execução para o script
chmod +x instalar_publicar_linux.sh

# 3. Execute como root
sudo ./instalar_publicar_linux.sh
```

> O script faz tudo automaticamente: instala pacotes, configura Apache, copia arquivos, cria o banco de dados e testa o sistema.

### 3.2. Método Manual — Passo a Passo

#### Passo 1: Conectar no servidor

```bash
# Se o servidor for remoto, use SSH:
ssh usuario@ip-do-servidor

# Se for local, apenas abra o terminal
```

#### Passo 2: Atualizar os pacotes do sistema

```bash
sudo apt update
```

> **O que faz:** Atualiza a lista de pacotes disponíveis nos repositórios do Ubuntu/Debian.

#### Passo 3: Instalar Apache, PHP 8 e SQLite

```bash
sudo apt install -y apache2 php php-sqlite3 php-mbstring php-xml libapache2-mod-php sqlite3 curl
```

> **O que faz:** Instala todos os pacotes necessários de uma vez.
> - O `-y` responde "sim" automaticamente para todas as perguntas.
> - O PHP virá na versão 8.x padrão do seu Ubuntu/Debian.

#### Passo 4: Verificar se tudo foi instalado corretamente

```bash
# Verificar Apache
apache2 -v
# Deve mostrar: Apache/2.4.xx

# Verificar PHP
php -v
# Deve mostrar: PHP 8.x

# Verificar extensões PHP
php -m | grep -E 'pdo_sqlite|mbstring|session'
# Deve mostrar: pdo_sqlite, mbstring, session
```

#### Passo 5: Ativar módulos extras do Apache

```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod expires
sudo a2enmod deflate
sudo systemctl restart apache2
```

> **O que cada módulo faz:**
> - `rewrite`: Permite URLs amigáveis (ex: `/login` em vez de `/index.php?url=login`)
> - `headers`: Adiciona cabeçalhos de segurança nas respostas
> - `expires`: Configura cache de arquivos estáticos (deixa o site mais rápido)
> - `deflate`: Comprime arquivos antes de enviar (economiza banda)

#### Passo 6: Verificar se o Apache está rodando

```bash
# Ver status
sudo systemctl status apache2

# Se não estiver rodando, inicie:
sudo systemctl start apache2

# Para iniciar automaticamente quando o servidor ligar:
sudo systemctl enable apache2
```

Acesse **http://ip-do-servidor/** no navegador. Deve aparecer a página padrão do Apache.

---

## 4. Estrutura de Diretórios no Servidor

```
/var/www/enfermagem/                      # 📂 Raiz do projeto (DocumentRoot)
│
├── index.html                            # 🏠 Landing page (página inicial)
├── index_estudos.html                    # 🗺️ Central de Estudos
├── guia_completo_estudos.html            # 📖 Guia Completo de Estudos (HTML)
├── Guia_Completo_Estudos_Introducao_Enfermagem.pdf
├── plano_de_estudos_20h.html             # 📘 Plano de Estudos 20h (HTML)
├── Plano_de_Estudos_20h_Introducao_Enfermagem.pdf
├── plano_estudos_avancado.html           # 🎯 Plano EAD Avançado (HTML)
├── Plano_Estudos_Avancado_EAD_Introducao_Enfermagem.pdf
├── resumo_uma_pagina.html                # 📋 Resumo de 5 min (HTML)
├── Resumo_Introducao_Enfermagem_5min.pdf
├── flashcards_enfermagem.html            # 🃏 Flashcards (HTML)
├── Flashcards_Introducao_Enfermagem.pdf
├── mapa_mental_10_modulos.html           # 🧠 Mapa Mental (HTML)
├── Mapa_Mental_10_Modulos_Introducao_Enfermagem.pdf
├── cronograma_mensal.html                # 📅 Cronograma Mensal (HTML)
├── Cronograma_Mensal_Estudos_Introducao_Enfermagem.pdf
├── casos_clinicos_pratica.html           # 🏥 Casos Clínicos (HTML)
├── Casos_Clinicos_Pratica_Introducao_Enfermagem.pdf
├── videos_recomendados.html              # 🎬 Guia de Vídeos (HTML)
├── Guia_Videos_Recomendados_Introducao_Enfermagem.pdf
├── simulado_40_questoes.html             # 📝 Simulado (HTML)
├── Simulado_40_Questoes_Introducao_Enfermagem.pdf
├── capa_apostila.html                    # 📄 Capa da Apostila
├── Apostila_Completa_Introducao_Enfermagem.pdf  # 📚 Compilado completo
├── anotacao.txt                          # 📝 Notas de aula
├── GUIA_PUBLICACAO_APACHE.md             # 📋 Este guia
│
└── sistema_avaliacao/                    # 🌐 Sistema de Avaliação PHP
    └── php/
        ├── index.php                     # 🎯 Front controller (roteador)
        ├── config.php                    # ⚙️ Configurações do sistema
        ├── db.php                        # 🗄️ Conexão PDO SQLite
        ├── functions.php                 # 🔧 Funções (auth, CSRF, helpers)
        ├── router.php                    # 🚀 Roteador para PHP built-in server
        ├── .htaccess                     # 📜 Regras de rewrite para Apache
        ├── avaliacao.db                  # 🗄️ Banco SQLite (2.475 questões)
        ├── README.md                     # 📋 Documentação do sistema
        ├── assets/
        │   ├── css/style.css             # 🎨 Folha de estilos
        │   └── js/app.js                 # ⚡ JavaScript do sistema
        ├── views/                        # 📄 Templates PHP
        │   ├── index.php                 # Página inicial
        │   ├── cadastro.php              # Cadastro de estudantes
        │   ├── login.php                 # Login
        │   ├── painel.php                # Painel do estudante
        │   ├── avaliacao.php             # Tela de avaliação
        │   ├── resultado.php             # Resultado da avaliação
        │   ├── progresso.php             # Progresso por módulo
        │   ├── recuperar_acesso.php      # Recuperação de senha
        │   └── admin.php                 # Painel administrativo
        ├── scripts/                      # 🔧 Scripts de manutenção
        │   ├── recriar_questoes.php      # Recria banco (2.475 questões)
        │   └── questions_data.php        # Banco de questões fonte
        └── tests/                        # 🧪 Testes PHPUnit
            ├── bootstrap.php
            ├── DatabaseTest.php
            ├── AuthTest.php
            ├── AvaliacaoTest.php
            └── IntegrationTest.php
```

### 4.1. Copiar os arquivos para o servidor

```bash
# 📌 IMPORTANTE: Execute na sua máquina LOCAL (onde estão os arquivos)

# Opção 1: SCP (transferência direta via SSH) — MAIS COMUM
scp -r /caminho/local/dos/arquivos/* usuario@servidor:/var/www/enfermagem/

# Exemplo prático:
scp -r /home/joao/projetos/en430/* joao@192.168.1.100:/var/www/enfermagem/

# Opção 2: Git (se estiver em repositório)
git clone https://seu-repositorio.git /var/www/enfermagem

# Opção 3: Pendrive / compartilhamento de rede (NFS/Samba)
cp -r /mnt/rede/enfermagem/* /var/www/enfermagem/
```

### 4.2. Ajustar permissões (executar no SERVIDOR)

```bash
# Após copiar os arquivos, ajuste as permissões no servidor:
sudo chown -R www-data:www-data /var/www/enfermagem
sudo chmod -R 755 /var/www/enfermagem

# O banco SQLite precisa de permissão de escrita:
sudo chmod 664 /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db
```

> **O que são essas permissões?**
> - `www-data` é o usuário do Apache no Linux
> - `755` significa: dono pode ler/escrever/executar, outros podem ler/executar
> - `664` significa: dono e grupo podem ler/escrever, outros podem ler
> - O banco SQLite precisa de `664` porque o Apache precisa ESCREVER nele

---

## 5. Configuração Manual do Apache

> 💡 **Já executou o script automático?** Pule para a [seção 6](#6-publicação-dos-materiais-estáticos-htmlpdf).

### 5.1. Entendendo o Apache no Linux

O Apache no Linux organiza a configuração em:
- `/etc/apache2/apache2.conf` — Configuração principal
- `/etc/apache2/sites-available/` — Sites disponíveis (arquivos .conf)
- `/etc/apache2/sites-enabled/` — Sites ativos (atalhos para sites-available)
- `/etc/apache2/mods-available/` — Módulos disponíveis
- `/etc/apache2/mods-enabled/` — Módulos ativos
- `/var/www/` — Diretório padrão dos sites
- `/var/log/apache2/` — Logs de acesso e erro

### 5.2. Criar o arquivo de configuração do site

Crie o arquivo `/etc/apache2/sites-available/enfermagem.conf`:

```bash
sudo nano /etc/apache2/sites-available/enfermagem.conf
```

> 💡 **Dica para iniciantes:** `nano` é um editor de texto simples no terminal.
> - Use as setas do teclado para navegar
> - `Ctrl+O` para salvar, `Ctrl+X` para sair
> - Ou use `vim` se preferir

### 5.3. Copie e cole o conteúdo abaixo

```apache
<VirtualHost *:80>
    ServerName enfermagem.localhost

    # ─── Pasta onde estão os arquivos ─────────────────
    DocumentRoot /var/www/enfermagem

    # ─── Configurações da pasta raiz ─────────────────
    <Directory /var/www/enfermagem>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # ─── Página padrão (nesta ordem) ─────────────────
    DirectoryIndex index.php index.html index_estudos.html

    # ─── Configurações do sistema de avaliação ──────
    <Directory /var/www/enfermagem/sistema_avaliacao/php>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # ─── Cache de arquivos estáticos ────────────────
    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/html "access plus 1 day"
        ExpiresByType text/css "access plus 7 days"
        ExpiresByType application/javascript "access plus 7 days"
        ExpiresByType application/pdf "access plus 7 days"
        ExpiresByType image/png "access plus 30 days"
        ExpiresByType image/jpeg "access plus 30 days"
    </IfModule>

    # ─── Compressão de arquivos ─────────────────────
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/css application/javascript
        AddOutputFilterByType DEFLATE application/pdf text/plain
    </IfModule>

    # ─── Cabeçalhos de segurança ────────────────────
    <IfModule mod_headers.c>
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-Frame-Options "DENY"
        Header always set Referrer-Policy "strict-origin-when-cross-origin"
    </IfModule>

    # ─── Proteger arquivos sensíveis ──────────────
    <FilesMatch "\.(db|sqlite|md|txt|log|ini)$">
        Require all denied
    </FilesMatch>

    # ─── Logs ─────────────────────────────────────
    ErrorLog ${APACHE_LOG_DIR}/enfermagem_error.log
    CustomLog ${APACHE_LOG_DIR}/enfermagem_access.log combined
</VirtualHost>
```

### 5.4. Ativar o site e testar

```bash
# 1. Desativar o site padrão
sudo a2dissite 000-default.conf

# 2. Ativar o site da enfermagem
sudo a2ensite enfermagem.conf

# 3. Testar a sintaxe da configuração
sudo apache2ctl configtest
# Deve mostrar: "Syntax OK"

# 4. Se deu erro, verifique o que foi:
sudo apache2ctl configtest 2>&1

# 5. Recarregar o Apache (sem parar o serviço)
sudo systemctl reload apache2
# OU reiniciar completamente:
sudo systemctl restart apache2
```

### 5.5. Testar no navegador

```bash
# Obter o IP do servidor
ip addr show | grep inet | head -3
# Ou: hostname -I

# Acesse no navegador:
# http://IP_DO_SERVIDOR/
# http://IP_DO_SERVIDOR/sistema_avaliacao/php/
```

---

## 6. Publicação dos Materiais Estáticos (HTML/PDF)

### 6.1. Como funciona

Os materiais de estudo (arquivos `.html` e `.pdf`) são servidos diretamente pelo Apache. Não precisa de configuração especial — basta copiá-los para a pasta do projeto.

### 6.2. Copiar os arquivos

```bash
# Já fez isso na seção 4.1? Pule este passo.

# Entre na pasta de arquivos originais (no seu computador local)
cd /caminho/para/o/projeto

# Copie para o servidor
scp *.html *.pdf *.txt *.md usuario@servidor:/var/www/enfermagem/

# Exemplo prático:
scp *.html *.pdf joao@192.168.1.100:/var/www/enfermagem/
```

### 6.3. Verificar se os arquivos estão acessíveis

```bash
# No servidor, teste com curl:
curl -s -o /dev/null -w "index.html: HTTP %{http_code}\n" http://localhost/index.html
curl -s -o /dev/null -w "index_estudos.html: HTTP %{http_code}\n" http://localhost/index_estudos.html
curl -s -o /dev/null -w "Guia_Completo_Estudos_Introducao_Enfermagem.pdf: HTTP %{http_code}\n" \
    http://localhost/Guia_Completo_Estudos_Introducao_Enfermagem.pdf

# Todos devem retornar HTTP 200
```

### 6.4. Landing page personalizada

A página `index.html` na raiz é a página inicial do site. Ela contém links para:
- 🗺️ **Central de Estudos** (`index_estudos.html`)
- 🌐 **Sistema de Avaliação** (caminho relativo para o PHP)
- 📚 **Apostila Completa** (PDF para download)

---

## 7. Publicação do Sistema de Avaliação (PHP + Apache)

### 7.1. Como funciona o roteamento PHP

O sistema PHP usa um **Front Controller**: todas as requisições passam por um único arquivo (`index.php`), que decide qual view carregar baseado na URL.

```
URL recebida: /sistema_avaliacao/php/cadastro
                   │
Apache verifica: o arquivo /sistema_avaliacao/php/cadastro existe?
                   │
              ┌────┴────┐
              │         │
             SIM       NÃO
              │         │
              ▼         ▼
        Serve o     .htaccess redireciona
        arquivo     para index.php
        direto           │
                         ▼
                    index.php analisa
                    a URL "cadastro"
                         │
                         ▼
                    Carrega a view
                    views/cadastro.php
```

### 7.2. O arquivo .htaccess

O sistema já inclui um `.htaccess` em `sistema_avaliacao/php/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Se o arquivo/diretório existe, serve diretamente
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    # Se não existe, redireciona para index.php
    RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>

# Bloquear acesso a arquivos sensíveis
<FilesMatch "\.(db|sqlite|md|txt|log|ini)$">
    Require all denied
</FilesMatch>
```

> **IMPORTANTE:** O `.htaccess` funciona apenas se:
> 1. O módulo `mod_rewrite` estiver ativo (`sudo a2enmod rewrite`)
> 2. O VirtualHost tiver `AllowOverride All` (configurado na seção 5.3)

### 7.3. Recriar o banco de dados

```bash
# Entre na pasta do sistema PHP
cd /var/www/enfermagem/sistema_avaliacao/php

# Recriar o banco (2.475 questões de enfermagem)
php scripts/recriar_questoes.php

# Verificar se o banco foi criado
php -r "\$db = new PDO('sqlite:avaliacao.db'); echo 'Total: ' . \$db->query('SELECT COUNT(*) FROM questoes')->fetchColumn() . ' questões' . PHP_EOL;"
# Deve retornar: Total: 2475 questões

# Ajustar permissão (IMPORTANTE: Apache precisa escrever no banco)
sudo chown www-data:www-data avaliacao.db
sudo chmod 664 avaliacao.db
```

### 7.4. Testar o sistema de avaliação

```bash
# Testar com curl (já deve estar funcionando se o Apache estiver rodando)

# Página inicial
curl -s -o /dev/null -w "Inicial: HTTP %{http_code}\n" http://localhost/sistema_avaliacao/php/

# Cadastro
curl -s -o /dev/null -w "Cadastro: HTTP %{http_code}\n" http://localhost/sistema_avaliacao/php/cadastro

# Login
curl -s -o /dev/null -w "Login: HTTP %{http_code}\n" http://localhost/sistema_avaliacao/php/login

# Recuperar acesso
curl -s -o /dev/null -w "Recuperar: HTTP %{http_code}\n" http://localhost/sistema_avaliacao/php/recuperar-acesso

# Painel (deve redirecionar para login — normal)
curl -s -o /dev/null -w "Painel: HTTP %{http_code}\n" http://localhost/sistema_avaliacao/php/painel
```

### 7.5. Teste com servidor PHP embutido (alternativa)

Se o Apache não estiver disponível, use o servidor PHP embutido:

```bash
cd /var/www/enfermagem/sistema_avaliacao/php
php -S 0.0.0.0:8080 -t . router.php
```

Acesse: **http://IP_DO_SERVIDOR:8080/**

> ⚠️ **Apenas para testes!** O servidor PHP embutido é mono-thread (processa uma requisição por vez). Use Apache em produção.

### 7.6. Testes automatizados (PHPUnit)

```bash
# Entre na pasta do sistema
cd /var/www/enfermagem/sistema_avaliacao/php

# Se o Composer estiver instalado e as dependências baixadas:
php vendor/bin/phpunit --no-coverage

# Resultado esperado:
# OK (61 tests, 199 assertions)
```

---

## 8. VirtualHost Completo — Exemplo

Arquivo: `/etc/apache2/sites-available/enfermagem.conf`

```apache
<VirtualHost *:80>
    ServerName enfermagem.seu-dominio.com
    ServerAdmin admin@seu-dominio.com

    # ─── Document Root (Materiais Estáticos) ───
    DocumentRoot /var/www/enfermagem

    <Directory /var/www/enfermagem>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # ─── Página padrão ───
    DirectoryIndex index.php index.html index_estudos.html

    # ─── Sistema de Avaliação PHP ───
    <Directory /var/www/enfermagem/sistema_avaliacao/php>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # ─── Cache ───
    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/html "access plus 1 day"
        ExpiresByType application/pdf "access plus 7 days"
        ExpiresByType text/css "access plus 7 days"
        ExpiresByType application/javascript "access plus 7 days"
        ExpiresByType image/png "access plus 30 days"
        ExpiresByType image/jpeg "access plus 30 days"
    </IfModule>

    # ─── Compressão ───
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/css application/javascript
        AddOutputFilterByType DEFLATE application/pdf text/plain
    </IfModule>

    # ─── Segurança ───
    <IfModule mod_headers.c>
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-Frame-Options "DENY"
        Header always set X-XSS-Protection "1; mode=block"
        Header always set Referrer-Policy "strict-origin-when-cross-origin"
        Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
    </IfModule>

    # ─── Proteger arquivos sensíveis ───
    <FilesMatch "\.(db|sqlite|md|txt|log|ini)$">
        Require all denied
    </FilesMatch>

    # ─── Logs ───
    ErrorLog ${APACHE_LOG_DIR}/enfermagem_error.log
    CustomLog ${APACHE_LOG_DIR}/enfermagem_access.log combined
</VirtualHost>
```

### 8.1. Ativar o site e testar

```bash
# Ativar o site
sudo a2ensite enfermagem.conf

# Se houver outro site ativo, desative:
sudo a2dissite 000-default.conf

# Testar configuração
sudo apache2ctl configtest

# Se "Syntax OK", recarregue:
sudo systemctl reload apache2

# Verificar logs se algo deu errado:
sudo tail -f /var/log/apache2/error.log
```

---

## 9. Segurança

### 9.1. Proteger arquivos sensíveis

O `.htaccess` já bloqueia acesso a arquivos `.db`, `.sqlite`, `.md`, `.txt`, `.log`, `.ini`.  
Para proteção extra, adicione no VirtualHost:

```apache
<FilesMatch "\.(db|sqlite|md|txt|log|ini)$">
    Require all denied
</FilesMatch>

# Impedir listagem de diretórios
<Directory /var/www/enfermagem>
    Options -Indexes
</Directory>
```

### 9.2. .htaccess na raiz do projeto

Crie `/var/www/enfermagem/.htaccess`:

```apache
# Proteger arquivos sensíveis
<FilesMatch "\.(db|sqlite|md|txt|log|ini)$">
    Require all denied
</FilesMatch>

# URLs amigáveis (opcional)
RewriteEngine On
RewriteRule ^estudos$ index_estudos.html [L]
RewriteRule ^avaliacao$ sistema_avaliacao/php/ [R,L]
```

### 9.3. HTTPS com Let's Encrypt (recomendado)

```bash
# Instalar Certbot
sudo apt install -y certbot python3-certbot-apache

# Obter certificado SSL (substitua pelo seu domínio)
sudo certbot --apache -d enfermagem.seu-dominio.com

# O Certbot configura tudo automaticamente!
# Seu site agora estará disponível em HTTPS

# Testar renovação automática
sudo certbot renew --dry-run
```

### 9.4. Firewall (UFW)

```bash
# Se o UFW estiver ativo, libere as portas:
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS

# Verificar status
sudo ufw status
```

### 9.5. Senhas no banco de dados

As senhas dos estudantes são armazenadas com **bcrypt** (cost 12):

> ✅ Senhas nunca armazenadas em texto plano  
> ✅ Salt automático para cada senha  
> ✅ Algoritmo bcrypt com cost 12 (recomendado pelo OWASP)  
> ✅ Proteção contra ataques de força bruta e rainbow tables

---

## 10. Manutenção e Backup

### 10.1. Backup do banco de dados (automático)

```bash
# Criar script de backup
sudo nano /usr/local/bin/backup_enfermagem.sh
```

Cole o conteúdo:

```bash
#!/bin/bash
# Backup automático do banco de dados do Sistema de Avaliação

DATA=$(date +%Y%m%d_%H%M)
ORIGEM="/var/www/enfermagem/sistema_avaliacao/php/avaliacao.db"
DESTINO="/var/backups/enfermagem/avaliacao-${DATA}.db"
LOG="/var/log/backup_enfermagem.log"

# Criar diretório de backup
mkdir -p /var/backups/enfermagem

# Copiar o banco
cp "$ORIGEM" "$DESTINO"

# Compactar para economizar espaço
gzip -f "$DESTINO"

# Registrar no log
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Backup criado: ${DESTINO}.gz" >> "$LOG"

# Manter apenas os últimos 30 backups
find /var/backups/enfermagem/ -name "*.gz" -mtime +30 -delete
```

```bash
# Dar permissão de execução
sudo chmod +x /usr/local/bin/backup_enfermagem.sh

# Testar
sudo /usr/local/bin/backup_enfermagem.sh

# Agendar no crontab (executar diariamente às 2h)
sudo crontab -e
# Adicione a linha:
0 2 * * * /usr/local/bin/backup_enfermagem.sh
```

### 10.2. Atualizar materiais (HTML/PDF)

```bash
# 1. Fazer backup dos materiais antigos
sudo cp /var/www/enfermagem/*.html /var/backups/enfermagem/materiais_$(date +%Y%m%d)/

# 2. Copiar novos materiais
sudo cp /caminho/novos/materiais/*.html /var/www/enfermagem/

# 3. Ajustar permissões
sudo chown www-data:www-data /var/www/enfermagem/*.html

# 4. Testar
curl -s -o /dev/null -w "%{http_code}" http://localhost/novo_material.html
```

### 10.3. Atualizar o sistema de avaliação

```bash
# 1. Fazer backup do banco ANTES de atualizar
sudo cp /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db \
       /var/backups/enfermagem/avaliacao_pre_update_$(date +%Y%m%d).db

# 2. Entrar na pasta do sistema
cd /var/www/enfermagem/sistema_avaliacao/php

# 3. Atualizar código (se estiver usando Git)
sudo git pull

# 4. OU copiar manualmente os novos arquivos
# sudo cp -r /caminho/novos/arquivos/* .

# 5. Recriar banco (⚠️ PERDE dados de estudantes e avaliações!)
#    Só faça se houver mudanças no banco de questões
# sudo php scripts/recriar_questoes.php

# 6. Ajustar permissões
sudo chown -R www-data:www-data /var/www/enfermagem/sistema_avaliacao/php
sudo chmod 664 /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db

# 7. Testar
curl -s -o /dev/null -w "%{http_code}" http://localhost/sistema_avaliacao/php/
```

### 10.4. Monitoramento básico

```bash
# Verificar se o Apache está rodando
sudo systemctl status apache2

# Verificar logs do sistema de avaliação
sudo tail -f /var/log/apache2/enfermagem_error.log
sudo tail -f /var/log/apache2/enfermagem_access.log

# Verificar espaço em disco
df -h /var/www
du -sh /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db

# Testar resposta do sistema
curl -s -o /dev/null -w "Landing: %{http_code}\n" http://localhost/
curl -s -o /dev/null -w "Sistema: %{http_code}\n" http://localhost/sistema_avaliacao/php/

# Listar processos Apache
ps aux | grep apache
```

---

## 11. Solução de Problemas

### ❌ "PHP não encontrado" ou "comando php não encontrado"

**Causa:** PHP não está instalado.  
**Solução:**
```bash
sudo apt update
sudo apt install -y php php-sqlite3 php-mbstring libapache2-mod-php
```

### ❌ "403 Forbidden" ao acessar o site

**Causa 1:** Permissões de diretório incorretas.  
**Solução:**
```bash
sudo chmod 755 /var/www/enfermagem
sudo chown -R www-data:www-data /var/www/enfermagem
```

**Causa 2:** VirtualHost sem `Require all granted`.  
**Solução:** Verifique se o VirtualHost tem:
```apache
<Directory /var/www/enfermagem>
    Require all granted
</Directory>
```

### ❌ "500 Internal Server Error" no sistema de avaliação

**Causa 1:** Permissão no banco SQLite.  
**Solução:**
```bash
sudo chown www-data:www-data /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db
sudo chmod 664 /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db
```

**Causa 2:** Erro no código PHP.  
**Solução:**
```bash
# Verificar logs do Apache
sudo tail -f /var/log/apache2/enfermagem_error.log

# Validar sintaxe PHP
php -l /var/www/enfermagem/sistema_avaliacao/php/index.php
php -l /var/www/enfermagem/sistema_avaliacao/php/functions.php

# Ativar exibição de erros (APENAS para debug)
# Edite /etc/php/8.x/apache2/php.ini e altere:
# display_errors = On
# display_startup_errors = On
# Depois: sudo systemctl restart apache2
```

### ❌ "404 Not Found" nas rotas do sistema

**Causa:** O `.htaccess` não está sendo processado.  
**Solução:**
```bash
# 1. Verificar se mod_rewrite está ativo
apache2ctl -M | grep rewrite
# Se não aparecer:
sudo a2enmod rewrite
sudo systemctl restart apache2

# 2. Verificar se AllowOverride está ativo no VirtualHost
# Deve conter: AllowOverride All

# 3. Verificar se o .htaccess existe
ls -la /var/www/enfermagem/sistema_avaliacao/php/.htaccess
```

### ❌ Página PHP aparece em branco

**Causa:** Erro fatal no PHP sem exibição.  
**Solução:**
```bash
# 1. Verificar logs
sudo tail -f /var/log/apache2/enfermagem_error.log

# 2. Executar o PHP manualmente para ver o erro
php /var/www/enfermagem/sistema_avaliacao/php/index.php

# 3. Verificar extensões necessárias
php -m | grep -E 'pdo_sqlite|mbstring|session|sqlite3'
```

### ❌ "could not find driver" (PDO SQLite)

**Causa:** Extensão `pdo_sqlite` não instalada.  
**Solução:**
```bash
sudo apt install -y php-sqlite3
sudo systemctl restart apache2
php -m | grep pdo_sqlite
```

### ❌ Apache não inicia após configurar

**Causa:** Erro de sintaxe no `httpd.conf` ou `sites-available`.  
**Solução:**
```bash
# Testar configuração
sudo apache2ctl configtest

# Verificar logs
sudo tail -f /var/log/apache2/error.log

# Causas comuns:
# - Porta 80 já em uso (sudo netstat -tulpn | grep :80)
# - Erro de digitação no arquivo de configuração
# - Módulo não encontrado
```

### ❌ Sistema sem CSS/JavaScript

**Causa:** Caminhos relativos incorretos ou `.htaccess` não processado.  
**Solução:**
```bash
# 1. Verificar se os assets existem
ls -la /var/www/enfermagem/sistema_avaliacao/php/assets/css/
ls -la /var/www/enfermagem/sistema_avaliacao/php/assets/js/

# 2. Abrir o console do navegador (F12) e verificar erros 404

# 3. Verificar as URLs geradas — a função url() em functions.php
#    deve produzir caminhos como:
#    /sistema_avaliacao/php/assets/css/style.css
```

### ❌ Banco de dados corrompido

**Causa:** Falha durante escrita no SQLite.  
**Solução:**
```bash
cd /var/www/enfermagem/sistema_avaliacao/php

# Verificar integridade
php -r "\$db = new PDO('sqlite:avaliacao.db'); echo 'Integrity: '; echo \$db->query('PRAGMA integrity_check')->fetchColumn(); echo PHP_EOL;"
# Deve retornar "ok"

# Se corrompido, recriar (⚠️ PERDE DADOS)
php scripts/recriar_questoes.php
sudo chown www-data:www-data avaliacao.db
sudo chmod 664 avaliacao.db
```

---

## 12. Checklist Pós-Deploy

Use esta lista para verificar se tudo está funcionando após a instalação.

### 🔲 12.1. Servidor e Serviços

- [ ] Apache está rodando: `sudo systemctl status apache2`
- [ ] Apache inicia automaticamente: `sudo systemctl is-enabled apache2`
- [ ] PHP 8+ instalado: `php -v`
- [ ] Extensões PHP: `php -m | grep -E 'pdo_sqlite|mbstring|session'`

### 🔲 12.2. Arquivos e Permissões

- [ ] Landing page presente: `ls -la /var/www/enfermagem/index.html`
- [ ] Sistema PHP presente: `ls -la /var/www/enfermagem/sistema_avaliacao/php/index.php`
- [ ] Banco de dados presente: `ls -la /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db`
- [ ] Permissões corretas: `ls -la /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db` (deve mostrar `-rw-rw-r--` ou `664`)
- [ ] Dono correto: `stat -c '%U:%G' /var/www/enfermagem/` (deve mostrar `www-data:www-data`)
- [ ] .htaccess presente: `ls -la /var/www/enfermagem/sistema_avaliacao/php/.htaccess`

### 🔲 12.3. Configuração do Apache

- [ ] VirtualHost configurado: `ls -la /etc/apache2/sites-available/enfermagem.conf`
- [ ] Site ativo: `ls -la /etc/apache2/sites-enabled/enfermagem.conf`
- [ ] Módulos ativos: `apache2ctl -M | grep -E 'rewrite|headers|expires|deflate'`
- [ ] Sintaxe OK: `sudo apache2ctl configtest` (deve retornar "Syntax OK")

### 🔲 12.4. Acesso Web

- [ ] Landing page acessível: `curl -s -o /dev/null -w "%{http_code}" http://localhost/` (deve ser 200)
- [ ] Sistema de avaliação acessível: `curl -s -o /dev/null -w "%{http_code}" http://localhost/sistema_avaliacao/php/` (deve ser 200)
- [ ] Página de cadastro acessível: `curl -s -o /dev/null -w "%{http_code}" http://localhost/sistema_avaliacao/php/cadastro` (deve ser 200)
- [ ] Página de login acessível: `curl -s -o /dev/null -w "%{http_code}" http://localhost/sistema_avaliacao/php/login` (deve ser 200)
- [ ] Arquivo PDF acessível: `curl -s -o /dev/null -w "%{http_code}" http://localhost/Apostila_Completa_Introducao_Enfermagem.pdf` (deve ser 200)

### 🔲 12.5. Banco de Dados

- [ ] Questões carregadas: `php -r "\$db = new PDO('sqlite:/var/www/enfermagem/sistema_avaliacao/php/avaliacao.db'); echo \$db->query('SELECT COUNT(*) FROM questoes')->fetchColumn();"` (deve mostrar 2475)
- [ ] Integridade OK: `php -r "\$db = new PDO('sqlite:/var/www/enfermagem/sistema_avaliacao/php/avaliacao.db'); echo \$db->query('PRAGMA integrity_check')->fetchColumn();"` (deve mostrar "ok")
- [ ] Backup configurado: `ls -la /usr/local/bin/backup_enfermagem.sh`
- [ ] Backup no crontab: `sudo crontab -l | grep backup`

### 🔲 12.6. Segurança

- [ ] Firewall configurado: `sudo ufw status | grep -E '80|443'`
- [ ] Arquivos sensíveis protegidos: `curl -s -o /dev/null -w "%{http_code}" http://localhost/sistema_avaliacao/php/avaliacao.db` (deve ser 403 ou 404)
- [ ] Listagem de diretórios desabilitada: `curl -s http://localhost/sistema_avaliacao/php/ | head -5` (não deve mostrar lista de arquivos)

---

## 13. Comandos Rápidos

```bash
# ─── APACHE ───────────────────────────────────────────────
sudo systemctl status apache2       # Verificar status
sudo systemctl start apache2        # Iniciar
sudo systemctl stop apache2         # Parar
sudo systemctl restart apache2      # Reiniciar
sudo systemctl reload apache2       # Recarregar config (sem parar)
sudo apache2ctl configtest          # Testar sintaxe
sudo apache2ctl -M                  # Listar módulos ativos

# ─── PHP ──────────────────────────────────────────────────
php -v                              # Versão do PHP
php -m                              # Módulos carregados
php -l arquivo.php                  # Validar sintaxe de um arquivo
php -S 0.0.0.0:8080 router.php      # Servidor PHP embutido (teste)

# ─── SISTEMA DE AVALIAÇÃO ────────────────────────────────
cd /var/www/enfermagem/sistema_avaliacao/php
php scripts/recriar_questoes.php    # Recriar banco de dados
php vendor/bin/phpunit --no-coverage # Executar testes

# ─── SCRIPTS DE CONTROLE ────────────────────────────────
sudo /usr/local/bin/controlar_enfermagem start    # Iniciar tudo
sudo /usr/local/bin/controlar_enfermagem stop     # Parar tudo
sudo /usr/local/bin/controlar_enfermagem restart  # Reiniciar
sudo /usr/local/bin/controlar_enfermagem status   # Status
sudo /usr/local/bin/controlar_enfermagem test     # Testar
sudo /usr/local/bin/controlar_enfermagem logs     # Ver logs
sudo /usr/local/bin/controlar_enfermagem info     # Informações

# ─── URLs PARA ACESSAR ─────────────────────────────────
# http://IP_DO_SERVIDOR/
# http://IP_DO_SERVIDOR/sistema_avaliacao/php/
# http://IP_DO_SERVIDOR/sistema_avaliacao/php/cadastro
# http://IP_DO_SERVIDOR/sistema_avaliacao/php/login
# http://IP_DO_SERVIDOR/sistema_avaliacao/php/painel
# http://IP_DO_SERVIDOR/sistema_avaliacao/php/recuperar-acesso
# http://IP_DO_SERVIDOR/sistema_avaliacao/php/admin
```

---

## 📄 Referências

- [Documentação Apache HTTP Server](https://httpd.apache.org/docs/)
- [Documentação PHP](https://www.php.net/manual/pt_BR/)
- [PHP — PDO SQLite](https://www.php.net/manual/pt_BR/ref.pdo-sqlite.php)
- [Let's Encrypt / Certbot](https://certbot.eff.org/)
- [Documentação do Sistema de Avaliação](sistema_avaliacao/php/README.md)
- [Central de Estudos](index_estudos.html)
- [Guia Windows (se o servidor for Windows)](GUIA_PUBLICACAO_APACHE_WINDOWS.md)

---

## 🎯 Resumo para Iniciantes

### 📋 Checklist de verificação

Após concluir a instalação, utilize o **[checklist de verificação pós-deploy](checklist_verificacao.md)** para confirmar que todos os componentes estão funcionando corretamente.

Se você é iniciante e só quer o sistema rodando:

```bash
# 1. Conecte no servidor
ssh usuario@ip_do_servidor

# 2. Execute o script automático
cd /caminho/para/o/projeto
chmod +x instalar_publicar_linux.sh
sudo ./instalar_publicar_linux.sh

# 3. Acesse no navegador
#    http://IP_DO_SERVIDOR/
#    http://IP_DO_SERVIDOR/sistema_avaliacao/php/

# Pronto! 🎉
```

---

*Guia de publicação para Linux (Ubuntu/Debian) elaborado para a disciplina **Introdução à Enfermagem (EN_430)***  
*Curso de Enfermagem — EAD/Subsequente • Julho 2026*  
*PHP 8 + Apache + SQLite • **Reformulado: todas as referências Python/Flask removidas***  
*Material organizado para **Estudos e Aprendizado** 🎓*  

---

### 📋 Documentos Relacionados

- [`checklist_verificacao.md`](checklist_verificacao.md) — Checklist de verificação pós-deploy
- [`GUIA_PUBLICACAO_APACHE_WINDOWS.md`](GUIA_PUBLICACAO_APACHE_WINDOWS.md) — Guia para servidores Windows
- [`sistema_avaliacao/php/README.md`](sistema_avaliacao/php/README.md) — Documentação do sistema PHP
