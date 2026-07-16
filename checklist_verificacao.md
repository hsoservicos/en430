# ✅ Checklist de Verificação Pós-Deploy

> **Sistema de Avaliação — Introdução à Enfermagem (EN_430)**  
> **PHP 8 + Apache + SQLite**  
> **Use esta lista para verificar se a instalação foi concluída corretamente**

---

## 📋 Como usar

1. Imprima ou abra este checklist em outro dispositivo
2. Marque cada item à medida que for verificado
3. Se algum item falhar, consulte a seção "Solução de Problemas" no guia correspondente
4. Assine e date ao final

---

## 🔲 1. Servidor — Sistema Operacional

### 🔲 1.1. Linux (Ubuntu/Debian)

- [ ] Apache está rodando: `sudo systemctl status apache2` → deve mostrar `active (running)`
- [ ] Apache inicia automaticamente: `sudo systemctl is-enabled apache2` → deve mostrar `enabled`
- [ ] PHP 8+ instalado: `php -v` → deve mostrar `PHP 8.x`
- [ ] Módulos Apache ativos:
  ```bash
  apache2ctl -M | grep -E 'rewrite|headers|expires|deflate|php'
  # Deve mostrar todos os 5
  ```
- [ ] Extensões PHP instaladas:
  ```bash
  php -m | grep -E 'pdo_sqlite|mbstring|session'
  # Deve mostrar os 3
  ```
- [ ] Firewall configurado: `sudo ufw status | grep 80` → deve mostrar porta 80 liberada
- [ ] Data/hora do servidor correta: `date`

### 🔲 1.2. Windows

- [ ] Apache funcionando: `C:\Apache24\bin\httpd.exe -t` → deve retornar "Syntax OK"
- [ ] Serviço Apache instalado: `sc query Apache2.4 | findstr STATE` → deve mostrar `RUNNING`
- [ ] PHP 8+ instalado: `php -v` → deve mostrar `PHP 8.x`
- [ ] Extensões PHP no php.ini:
  - `extension=php_pdo_sqlite.dll`
  - `extension=php_sqlite3.dll`
  - `extension=php_mbstring.dll`
- [ ] PHP carregado no Apache: `http://localhost/info.php` (criar temporariamente) → deve mostrar `PHP 8.x`
- [ ] Firewall configurado: `netsh advfirewall firewall show rule name="Apache HTTP (80)"` → deve existir
- [ ] Visual C++ Redistributable instalado (64-bit)

---

## 🔲 2. Arquivos do Projeto

- [ ] Landing page presente:
  - **Linux:** `ls -la /var/www/enfermagem/index.html`
  - **Windows:** `dir C:\Apache24\htdocs\en430\index.html`
- [ ] Sistema PHP presente:
  - **Linux:** `ls -la /var/www/enfermagem/sistema_avaliacao/php/index.php`
  - **Windows:** `dir C:\Apache24\htdocs\en430\sistema_avaliacao\php\index.php`
- [ ] Banco de dados presente:
  - **Linux:** `ls -lh /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db`
  - **Windows:** `dir C:\Apache24\htdocs\en430\sistema_avaliacao\php\avaliacao.db`
- [ ] .htaccess presente:
  - `ls -la /var/www/enfermagem/sistema_avaliacao/php/.htaccess` (Linux)
  - `dir C:\Apache24\htdocs\en430\sistema_avaliacao\php\.htaccess` (Windows)
- [ ] Assets (CSS/JS) presentes:
  - `assets/css/style.css`
  - `assets/js/app.js`

---

## 🔲 3. Permissões

### 🔲 3.1. Linux

- [ ] Dono da pasta: `stat -c '%U:%G' /var/www/enfermagem/` → deve ser `www-data:www-data`
- [ ] Permissão da pasta: `stat -c '%a' /var/www/enfermagem/` → deve ser `755`
- [ ] Permissão do banco: `stat -c '%a' /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db` → deve ser `664`
- [ ] Dono do banco: `stat -c '%U:%G' /var/www/enfermagem/sistema_avaliacao/php/avaliacao.db` → deve ser `www-data:www-data`
- [ ] Apache pode ler o banco: `sudo -u www-data php -r "echo 'OK';"` (na pasta do banco)
- [ ] Apache pode escrever no banco: `sudo -u www-data php -r "file_put_contents('teste.txt', 'teste'); unlink('teste.txt'); echo 'OK';"` (na pasta do banco)

### 🔲 3.2. Windows

- [ ] Permissões NTFS corretas:
  ```cmd
  icacls C:\Apache24\htdocs\en430\sistema_avaliacao\php\avaliacao.db
  # BUILTIN\Users deve ter (RW)
  ```

---

## 🔲 4. Configuração do Apache

### 🔲 4.1. Linux

- [ ] VirtualHost existe: `ls -la /etc/apache2/sites-available/enfermagem.conf`
- [ ] Site ativo: `ls -la /etc/apache2/sites-enabled/enfermagem.conf`
- [ ] Site padrão desativado: `ls -la /etc/apache2/sites-enabled/000-default.conf` → não deve existir (ou estar linkado para outro)
- [ ] Sintaxe OK: `sudo apache2ctl configtest` → "Syntax OK"
- [ ] Site padrão desativado: `ls -la /etc/apache2/sites-enabled/000-default.conf 2>&1` → deve mostrar erro "No such file"

### 🔲 4.2. Windows

- [ ] httpd.conf tem `LoadModule php_module`: `findstr LoadModule C:\Apache24\conf\httpd.conf | findstr php`
- [ ] Módulos ativos no httpd.conf:
  - `LoadModule rewrite_module modules/mod_rewrite.so`
  - `LoadModule headers_module modules/mod_headers.so`
  - `LoadModule expires_module modules/mod_expires.so`
  - `LoadModule deflate_module modules/mod_deflate.so`
- [ ] `AllowOverride All` configurado no VirtualHost
- [ ] `DirectoryIndex` inclui `index.php`

---

## 🔲 5. Acesso Web

> 💡 **Para Windows**, os caminhos das URLs iniciam com `/en430/` (ex: `http://localhost/en430/sistema_avaliacao/php/`).  
> **Para Linux**, use os caminhos abaixo (ex: `http://localhost/sistema_avaliacao/php/`).

- [ ] **Landing page:** `curl -s -o /dev/null -w "Landing: %{http_code}\n" http://localhost/`
  → Deve ser `200` ou `301`
- [ ] **Sistema de avaliação (raiz):** `curl -s -o /dev/null -w "Sistema: %{http_code}\n" http://localhost/sistema_avaliacao/php/`
  → Deve ser `200`
- [ ] **Cadastro:** `curl -s -o /dev/null -w "Cadastro: %{http_code}\n" http://localhost/sistema_avaliacao/php/cadastro`
  → Deve ser `200`
- [ ] **Login:** `curl -s -o /dev/null -w "Login: %{http_code}\n" http://localhost/sistema_avaliacao/php/login`
  → Deve ser `200`
- [ ] **Recuperar acesso:** `curl -s -o /dev/null -w "Recuperar: %{http_code}\n" http://localhost/sistema_avaliacao/php/recuperar-acesso`
  → Deve ser `200`
- [ ] **Painel (sem login):** `curl -s -o /dev/null -w "Painel: %{http_code}\n" http://localhost/sistema_avaliacao/php/painel`
  → Deve ser `302` (redireciona para login)
- [ ] **Logout:** `curl -s -o /dev/null -w "Logout: %{http_code}\n" http://localhost/sistema_avaliacao/php/logout`
  → Deve ser `302` (redireciona)
- [ ] **PDF acessível:** `curl -s -o /dev/null -w "PDF: %{http_code}\n" http://localhost/Apostila_Completa_Introducao_Enfermagem.pdf`
  → Deve ser `200`
- [ ] **CSS acessível:** `curl -s -o /dev/null -w "CSS: %{http_code}\n" http://localhost/sistema_avaliacao/php/assets/css/style.css`
  → Deve ser `200`

---

## 🔲 6. Banco de Dados

> 💡 **Antes de testar o banco, entre na pasta correta:**  
> **Linux:** `cd /var/www/enfermagem/sistema_avaliacao/php`  
> **Windows:** `cd C:\Apache24\htdocs\en430\sistema_avaliacao\php`

- [ ] **Total de questões:**
  ```bash
  php -r "\$db = new PDO('sqlite:avaliacao.db'); echo \$db->query('SELECT COUNT(*) FROM questoes')->fetchColumn();"
  ```
  → Deve ser **2475**
- [ ] **Integridade do banco:**
  ```bash
  php -r "\$db = new PDO('sqlite:avaliacao.db'); echo \$db->query('PRAGMA integrity_check')->fetchColumn();"
  ```
  → Deve ser **"ok"**
- [ ] **10 módulos presentes:**
  ```bash
  php -r "\$db = new PDO('sqlite:avaliacao.db'); foreach(range(1,10) as \$m) echo 'M'.\$m.': '.\$db->query(\"SELECT COUNT(*) FROM questoes WHERE modulo=\$m\")->fetchColumn().PHP_EOL;"
  ```
  → Todos os 10 módulos com questões
- [ ] **3 níveis de dificuldade:**
  ```bash
  php -r "\$db = new PDO('sqlite:avaliacao.db'); foreach(['Fácil','Médio','Difícil'] as \$d) echo \$d.': '.\$db->query(\"SELECT COUNT(*) FROM questoes WHERE dificuldade='\$d'\")->fetchColumn().PHP_EOL;"
  ```
  → Fácil, Médio e Difícil com questões

---

## 🔲 7. Segurança

- [ ] **Banco protegido:** `curl -s -o /dev/null -w "Banco: %{http_code}\n" http://localhost/sistema_avaliacao/php/avaliacao.db`
  → Deve ser **403** (Forbidden) ou **404**
- [ ] **.htaccess protegido:** `curl -s -o /dev/null -w "Htaccess: %{http_code}\n" http://localhost/sistema_avaliacao/php/.htaccess`
  → Deve ser **403**
- [ ] **Banco SQLite NÃO está acessível publicamente** (já testado acima)
- [ ] **Arquivos .md protegidos:** `curl -s -o /dev/null -w "Md: %{http_code}\n" http://localhost/sistema_avaliacao/php/README.md`
  → Deve ser **403** (Forbidden) — bloqueado pelo .htaccess
- [ ] **Arquivos .ini protegidos:** `curl -s -o /dev/null -w "Ini: %{http_code}\n" http://localhost/sistema_avaliacao/php/phpunit.xml`
  → Deve ser **403** (Forbidden) — bloqueado pelo .htaccess
- [ ] **Listagem de diretórios:** `curl -s http://localhost/sistema_avaliacao/php/ | head -5`
  → Não deve mostrar lista de arquivos (deve mostrar a página HTML do sistema)
- [ ] **Headers de segurança presentes:**
  ```bash
  curl -I http://localhost/sistema_avaliacao/php/ 2>/dev/null | grep -iE 'x-content-type|x-frame|x-xss|referrer'
  ```
  → Deve mostrar os cabeçalhos de segurança

---

## 🔲 8. Funcionalidades

### 🔲 8.1. Cadastro

- [ ] Formulário de cadastro carrega: `curl -s http://localhost/sistema_avaliacao/php/cadastro | grep -i 'form'` → deve encontrar `<form`
- [ ] Cadastro via POST (testar com curl):
  ```bash
  curl -s -X POST -d "nome=Teste&email=teste@teste.com&senha=1234&confirmar_senha=1234" \
    -c /tmp/cookies.txt -b /tmp/cookies.txt \
    http://localhost/sistema_avaliacao/php/cadastro -o /dev/null -w '%{http_code}'
  ```
  → Deve retornar 302 (redirecionamento) ou 200 com mensagem

### 🔲 8.2. Login

- [ ] Formulário de login carrega
- [ ] Login válido funciona:
  ```bash
  curl -s -X POST -d "email=teste@teste.com&senha=1234" \
    -c /tmp/cookies.txt -b /tmp/cookies.txt \
    http://localhost/sistema_avaliacao/php/login -o /dev/null -w '%{http_code}'
  ```
  → Deve retornar 302 (redireciona para painel)

### 🔲 8.3. Avaliação

- [ ] Página de avaliação carrega (com sessão ativa)
- [ ] Questões são exibidas corretamente
- [ ] É possível responder e finalizar a avaliação
- [ ] Resultado mostra pontuação e gabarito

### 🔲 8.4. Progresso

- [ ] Gráfico de progresso carrega (com sessão ativa)
- [ ] Totais por módulo são exibidos corretamente

---

## 🔲 9. Testes Automatizados (Opcional)

- [ ] PHPUnit instalado: `ls sistema_avaliacao/php/vendor/bin/phpunit`
- [ ] Testes passando:
  ```bash
  cd sistema_avaliacao/php
  php vendor/bin/phpunit --no-coverage
  ```
  → Deve mostrar: **"OK (61 tests, 199 assertions)"**

---

## 🔲 10. Manutenção

- [ ] Backup automático configurado:
  - **Linux:** `sudo crontab -l | grep backup` → deve mostrar a linha do backup
  - **Windows:** Agendador de Tarefas deve ter a tarefa "Backup Avaliação"
- [ ] Script de backup existe:
  - **Linux:** `ls -la /usr/local/bin/backup_enfermagem.sh`
  - **Windows:** `dir C:\scripts\backup_avaliacao.bat`
- [ ] Script de controle existe:
  - **Linux:** `ls -la /usr/local/bin/controlar_enfermagem`
  - **Windows:** `dir sistema_avaliacao\php\controlar_servico.ps1`

---

## 🔲 11. Verificação Visual (Navegador)

- [ ] Acessar a landing page: os links funcionam?
- [ ] Acessar a Central de Estudos: todos os materiais aparecem?
- [ ] Acessar o Sistema de Avaliação: o layout está correto?
- [ ] Testar cadastro com dados reais
- [ ] Testar login
- [ ] Iniciar avaliação e responder algumas questões
- [ ] Visualizar resultado
- [ ] Visualizar progresso
- [ ] Verificar se não há erros no console do navegador (F12 → Console)
- [ ] Testar em outro dispositivo/navegador na mesma rede
- [ ] Testar responsividade (redimensionar a janela)

---

## 📝 Resumo da Instalação

| Item | Status | Observação |
|:-----|:------:|:-----------|
| Servidor configurado | ✅ / ❌ | |
| Arquivos copiados | ✅ / ❌ | |
| Permissões ajustadas | ✅ / ❌ | |
| Apache configurado | ✅ / ❌ | |
| Banco criado (2.475 questões) | ✅ / ❌ | |
| Landing page acessível | ✅ / ❌ | |
| Sistema de avaliação acessível | ✅ / ❌ | |
| Cadastro funcional | ✅ / ❌ | |
| Login funcional | ✅ / ❌ | |
| Avaliação funcional | ✅ / ❌ | |
| Segurança verificada | ✅ / ❌ | |
| Backup configurado | ✅ / ❌ | |
| Testes passando | ✅ / ❌ | |

**Total de itens:** _____ / 100%  
**Instalador:** _________________________________  
**Data:** ____ / ____ / ______  
**Servidor:** ___________________________________

---

*Checklist de verificação pós-deploy — Sistema de Avaliação EN_430*  
*PHP 8 + Apache + SQLite • Julho 2026*
