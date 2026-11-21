<#
+==============================================================================+
|                                                                              |
|  [Windows] Script de Instalação, Implantação e Publicação - Windows 10       |
|                                                                              |
|  Sistema de Avaliação - Introdução à Enfermagem (EN_430)                     |
|                                                                              |
|  Este script automatiza TODO o processo de deploy do sistema de              |
|  avaliação PHP 8 + Apache + SQLite no Windows 10.                            |
|                                                                              |
|  Funcionalidades:                                                            |
|    * Verificação de pré-requisitos                                           |
|    * Configuração do PHP 8 no Apache (mod_php)                               |
|    * Criação e população do banco de dados SQLite                            |
|    * Configuração do Apache HTTP Server                                      |
|    * Configuração do Firewall do Windows                                     |
|    * Teste de funcionamento                                                  |
|                                                                              |
|  Uso:                                                                        |
|    PowerShell(Admin): .\instalar_publicar_windows.ps1                        |
|                                                                              |
|  [!] Se o PowerShell bloquear a execução:                                   |
|    1. Botão direito no arquivo => Propriedades => Desbloquear                |
|    2. Ou: Set-ExecutionPolicy -Scope Process -ExecutionPolicy RemoteSigned   |
|    3. Ou: powershell -ExecutionPolicy RemoteSigned -File .\script.ps1        |
|                                                                              |
|  Requer:                                                                     |
|    * Windows 10/11 (64 bits)                                                 |
|    * PHP 8.1+ instalado (Thread Safe)                                        |
|    * Apache 2.4 (Apache Lounge ou XAMPP) instalado                           |
|    * Execução como Administrador                                             |
|                                                                              |
+==============================================================================+
#>

# =============================================================================
# CONFIGURAÇÕES - EDITAR CONFORME SEU AMBIENTE
# =============================================================================

$PROJECT_ROOT    = Split-Path -Parent $MyInvocation.MyCommand.Path
$PHP_DIR         = Join-Path $PROJECT_ROOT "sistema_avaliacao\php"

# Caminhos do Apache (AJUSTE conforme sua instalação)
# Apache Lounge:
$APACHE_DIR      = "C:\Apache24"
# XAMPP (descomente se usar XAMPP):
# $APACHE_DIR    = "C:\xampp\apache"

$APACHE_CONF     = Join-Path $APACHE_DIR "conf\httpd.conf"
$APACHE_HTDOCS   = Join-Path $APACHE_DIR "htdocs"
$APACHE_LOGS     = Join-Path $APACHE_DIR "logs"

# Caminho do PHP (AJUSTE conforme sua instalação)
$PHP_INSTALL_DIR = "C:\php"

# Configurações do servidor
$SERVER_PORT     = 80
$SERVER_NAME     = "localhost"

# Cores para output
$GREEN  = [ConsoleColor]::Green
$YELLOW = [ConsoleColor]::Yellow
$RED    = [ConsoleColor]::Red
$CYAN   = [ConsoleColor]::Cyan
$WHITE  = [ConsoleColor]::White

# =============================================================================
# FUNÇÕES AUXILIARES
# =============================================================================

function Write-Step {
    param([string]$Text)
    Write-Host "`n============================================================" -ForegroundColor $CYAN
    Write-Host "  $Text" -ForegroundColor $CYAN
    Write-Host "============================================================" -ForegroundColor $CYAN
}

function Write-OK {
    param([string]$Text)
    Write-Host "  [OK] $Text" -ForegroundColor $GREEN
}

function Write-Warn {
    param([string]$Text)
    Write-Host "  [!] $Text" -ForegroundColor $YELLOW
}

function Write-Error {
    param([string]$Text)
    Write-Host "  [ERRO] $Text" -ForegroundColor $RED
}

function Test-CommandExists {
    param([string]$Command)
    $exists = Get-Command $Command -ErrorAction SilentlyContinue
    return ($null -ne $exists)
}

function Test-Admin {
    $identity = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($identity)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

# =============================================================================
# CABEÇALHO
# =============================================================================
Clear-Host
Write-Host @"
+--------------------------------------------------------------------+
|                                                                    |
|    INSTALAÇÃO E PUBLICAÇÃO NO WINDOWS 10                            |
|                                                                    |
|   Sistema de Avaliação - Introdução à Enfermagem (EN_430)          |
|   PHP 8 + Apache + SQLite                                          |
|                                                                    |
+--------------------------------------------------------------------+
"@ -ForegroundColor $CYAN

# =============================================================================
# 1. VERIFICAR PRÉ-REQUISITOS
# =============================================================================
Write-Step "[?] 1. Verificando pré-requisitos..."

$ERROS = 0
$AVISOS = 0

# Verificar execução como Admin
if (-not (Test-Admin)) {
    Write-Error "Este script deve ser executado como Administrador!"
    Write-Host "  [Dica] Clique com botão direito no PowerShell e selecione 'Executar como Administrador'" -ForegroundColor $YELLOW
    $ERROS++
} else {
    Write-OK "Executando como Administrador"
}

# Verificar PHP
if (Test-CommandExists "php") {
    $phpVer = php -v 2>&1 | Select-Object -First 1
    if ($phpVer -match "PHP 8\.[0-9]") {
        Write-OK "PHP encontrado: $phpVer"
    } else {
        Write-Warn "PHP encontrado mas versão pode ser antiga: $phpVer"
        Write-Warn "  Recomendado: PHP 8.1+ (64-bit Thread Safe)"
        $AVISOS++
    }

    # Verificar extensões
    $modulos = php -m 2>&1
    if ($modulos -match "pdo_sqlite") {
        Write-OK "Extensão pdo_sqlite disponível"
    } else {
        Write-Error "pdo_sqlite não disponível! Habilite no php.ini: extension=php_pdo_sqlite.dll"
        $ERROS++
    }
    if ($modulos -match "mbstring") {
        Write-OK "Extensão mbstring disponível"
    } else {
        Write-Warn "mbstring não disponível. Habilite no php.ini: extension=php_mbstring.dll"
        $AVISOS++
    }
} else {
    Write-Error "PHP não encontrado! Instale PHP 8.1+ (64-bit Thread Safe)"
    Write-Error "  Baixe em: https://windows.php.net/download/"
    Write-Error "  Escolha a versão 'VS16 x64 Thread Safe'"
    $ERROS++
}

# Verificar Apache
if (Test-Path $APACHE_DIR) {
    $apacheExe = Join-Path $APACHE_DIR "bin\httpd.exe"
    if (Test-Path $apacheExe) {
        $apacheVersion = & $apacheExe -v 2>&1 | Select-Object -First 1
        Write-OK "Apache encontrado: $apacheVersion"
        Write-OK "  Diretório: $APACHE_DIR"
    } else {
        Write-Error "httpd.exe não encontrado em $apacheExe"
        $ERROS++
    }
} else {
    Write-Error "Apache não encontrado em $APACHE_DIR"
    Write-Error "  Baixe o Apache em: https://www.apachelounge.com/download/"
    Write-Error "  Extraia para $APACHE_DIR"
    $ERROS++
}

# Verificar diretório do projeto
if (Test-Path $PHP_DIR) {
    Write-OK "Diretório PHP do projeto encontrado: $PHP_DIR"
    if (Test-Path (Join-Path $PHP_DIR "index.php")) {
        Write-OK "Front controller (index.php) encontrado"
    } else {
        Write-Error "index.php não encontrado em $PHP_DIR"
        $ERROS++
    }
} else {
    Write-Error "Diretório PHP do projeto não encontrado em $PHP_DIR"
    $ERROS++
}

if ($ERROS -gt 0) {
    Write-Host "`n[ERRO] $ERROS erro(s) encontrado(s). Corrija-os e execute novamente." -ForegroundColor $RED
    if ($AVISOS -gt 0) {
        Write-Host "[!] $AVISOS aviso(s) - podem ser ignorados, mas verifique." -ForegroundColor $YELLOW
    }
    exit 1
}

Write-Host "`n[OK] Todos os pré-requisitos OK! Prosseguindo..." -ForegroundColor $GREEN

# =============================================================================
# 2. CONFIGURAR PHP NO APACHE (mod_php)
# =============================================================================
Write-Step "[Web] 2. Configurando PHP no Apache..."

if (-not (Test-Path $APACHE_CONF)) {
    Write-Error "Arquivo httpd.conf não encontrado em $APACHE_CONF"
    exit 1
}

# Fazer backup do httpd.conf original
$BACKUP_CONF = "$APACHE_CONF.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Copy-Item $APACHE_CONF $BACKUP_CONF
Write-OK "Backup do httpd.conf criado: $BACKUP_CONF"

# Ler o conteúdo atual
$httpdContent = Get-Content $APACHE_CONF -Raw

# Verificar se PHP já está configurado
if ($httpdContent -match "LoadModule php_module") {
    Write-OK "PHP já está configurado no httpd.conf"
} else {
    Write-Host "  Adicionando configuração do PHP ao httpd.conf..."

    $PHP_DLL = Join-Path $PHP_INSTALL_DIR "php8apache2_4.dll"
    if (-not (Test-Path $PHP_DLL)) {
        Write-Warn "php8apache2_4.dll não encontrado em $PHP_DLL"
        Write-Warn "  Verifique se instalou a versão 'Thread Safe' do PHP"
        $AVISOS++
    }

    # Adicionar configuração PHP ao final da seção de módulos
    $PHP_CONFIG = @"

# ─── PHP 8 ────────────────────────────────────────────────────────
LoadModule php_module "$($PHP_INSTALL_DIR -replace '\\', '/')/php8apache2_4.dll"
<FilesMatch \.php$>
    SetHandler application/x-httpd-php
</FilesMatch>

# Caminho para o php.ini
PHPIniDir "$($PHP_INSTALL_DIR -replace '\\', '/')"
"@

    # Inserir antes do final da seção de módulos
    $insertPoint = $httpdContent.LastIndexOf("LoadModule ")
    $insertPoint = $httpdContent.IndexOf("`n", $insertPoint) + 1
    $httpdContent = $httpdContent.Insert($insertPoint, $PHP_CONFIG)

    # Adicionar index.php ao DirectoryIndex se não existir
    if ($httpdContent -notmatch "index\.php") {
        $httpdContent = $httpdContent -replace "(DirectoryIndex\s+)", '$1index.php '
    }

    Set-Content -Path $APACHE_CONF -Value $httpdContent
    Write-OK "Configuração do PHP adicionada ao httpd.conf"
}

# =============================================================================
# 3. HABILITAR MÓDULOS APACHE NECESSÁRIOS
# =============================================================================
Write-Step "[Web] 3. Habilitando módulos do Apache..."

$modulosNecessarios = @(
    "mod_rewrite.so",
    "mod_headers.so",
    "mod_expires.so",
    "mod_deflate.so"
)

$httpdContent = Get-Content $APACHE_CONF -Raw
foreach ($mod in $modulosNecessarios) {
    if ($httpdContent -match "#.*LoadModule.*$mod") {
        $httpdContent = $httpdContent -replace "#.*(LoadModule.*$mod)", '$1'
        Write-OK "Módulo ativado: $mod"
    } elseif ($httpdContent -match "LoadModule.*$mod") {
        Write-OK "Módulo já ativo: $mod"
    } else {
        Write-Warn "Módulo não encontrado no httpd.conf: $mod"
        $AVISOS++
    }
}
Set-Content -Path $APACHE_CONF -Value $httpdContent

# =============================================================================
# 4. COPIAR ARQUIVOS PARA O DIRETÓRIO WEB
# =============================================================================
Write-Step "[Arquivos] 4. Copiando arquivos para o diretório web..."

$DEPLOY_DIR = Join-Path $APACHE_HTDOCS "en430"

# Criar diretório de deploy
New-Item -ItemType Directory -Path $DEPLOY_DIR -Force | Out-Null

# Copiar landing page e materiais
Write-Host "  Copiando landing page e materiais..."
Get-ChildItem "$PROJECT_ROOT\*.html" -ErrorAction SilentlyContinue | Copy-Item -Destination $DEPLOY_DIR -Force
Get-ChildItem "$PROJECT_ROOT\*.pdf" -ErrorAction SilentlyContinue | Copy-Item -Destination $DEPLOY_DIR -Force
Get-ChildItem "$PROJECT_ROOT\*.md" -ErrorAction SilentlyContinue | Copy-Item -Destination $DEPLOY_DIR -Force

# Copiar sistema de avaliação PHP
Write-Host "  Copiando sistema de avaliação PHP..."
$PHP_DEPLOY_DIR = Join-Path $DEPLOY_DIR "sistema_avaliacao\php"
if (Test-Path $PHP_DIR) {
    New-Item -ItemType Directory -Path $PHP_DEPLOY_DIR -Force | Out-Null
    Copy-Item "$PHP_DIR\*" $PHP_DEPLOY_DIR -Recurse -Force
    Write-OK "Sistema PHP copiado para $PHP_DEPLOY_DIR"
}

Write-OK "Arquivos copiados para $DEPLOY_DIR"

# =============================================================================
# 5. CRIAR E POPULAR O BANCO DE DADOS
# =============================================================================
Write-Step "[DB] 5. Criando e populando o banco de dados..."

$DB_PATH = Join-Path $PHP_DEPLOY_DIR "avaliacao.db"

if (Test-Path $DB_PATH) {
    Write-Warn "Banco de dados já existe"
    $choice = Read-Host "  Deseja recriar? (S/N) [N]"
    if ($choice -eq "S" -or $choice -eq "s") {
        Write-Host "  Removendo banco antigo..."
        Remove-Item $DB_PATH -Force -ErrorAction SilentlyContinue
        Write-Host "  Executando recriar_questoes.php..."
        Push-Location $PHP_DEPLOY_DIR
        & php scripts\recriar_questoes.php
        Pop-Location
        Write-OK "Banco de dados recriado com sucesso!"
    } else {
        Write-OK "Utilizando banco existente"
    }
} else {
    Write-Host "  Executando recriar_questoes.php..."
    Push-Location $PHP_DEPLOY_DIR
    & php scripts\recriar_questoes.php
    Pop-Location
    Write-OK "Banco de dados criado com sucesso!"
}

# Contar questões
try {
    Push-Location $PHP_DEPLOY_DIR
    $total = & php -r "\$db = new PDO('sqlite:avaliacao.db'); echo \$db->query('SELECT COUNT(*) FROM questoes')->fetchColumn();"
    Pop-Location
    Write-OK "Total de questões no banco: $total"
} catch {
    Write-Warn "Não foi possível contar as questões"
}

# =============================================================================
# 6. CONFIGURAR O APACHE — VIRTUALHOST
# =============================================================================
Write-Step "[Web] 6. Configurando VirtualHost no Apache..."

$VHOST_CONFIG = @"

# =============================================================================
# VirtualHost - Sistema de Avaliação EN_430
# Adicionado automaticamente em $(Get-Date)
# =============================================================================
<VirtualHost *:$SERVER_PORT>
    ServerName $SERVER_NAME
    DocumentRoot "$($DEPLOY_DIR -replace '\\', '/')"

    DirectoryIndex index.php index.html index_estudos.html

    <Directory "$($DEPLOY_DIR -replace '\\', '/')">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <Directory "$($PHP_DEPLOY_DIR -replace '\\', '/')">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/html "access plus 1 day"
        ExpiresByType text/css "access plus 7 days"
        ExpiresByType application/javascript "access plus 7 days"
        ExpiresByType application/pdf "access plus 7 days"
    </IfModule>

    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/css application/javascript
        AddOutputFilterByType DEFLATE application/pdf text/plain
    </IfModule>

    <IfModule mod_headers.c>
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-Frame-Options "DENY"
        Header always set Referrer-Policy "strict-origin-when-cross-origin"
    </IfModule>

    <FilesMatch "\.(db|sqlite|md|txt|log|ini)$">
        Require all denied
    </FilesMatch>

    ErrorLog "$($APACHE_LOGS -replace '\\', '/')/en430_error.log"
    CustomLog "$($APACHE_LOGS -replace '\\', '/')/en430_access.log" combined
</VirtualHost>
"@

# Verificar se o VirtualHost já existe
if ($httpdContent -match "en430") {
    Write-Warn "VirtualHost para en430 já existe no httpd.conf."
    $choice = Read-Host "  Deseja sobrescrever? (S/N) [N]"
    if ($choice -eq "S" -or $choice -eq "s") {
        Add-Content $APACHE_CONF $VHOST_CONFIG
        Write-OK "VirtualHost adicionado ao httpd.conf"
    } else {
        Write-OK "Mantendo configuração existente"
    }
} else {
    Add-Content $APACHE_CONF $VHOST_CONFIG
    Write-OK "VirtualHost adicionado ao httpd.conf"
}

# =============================================================================
# 7. CONFIGURAR FIREWALL DO WINDOWS
# =============================================================================
Write-Step "[Segurança] 7. Configurando Firewall do Windows..."

$RULE_NAME = "Apache HTTP Server (EN430)"
$existingRule = Get-NetFirewallRule -DisplayName $RULE_NAME -ErrorAction SilentlyContinue

if (-not $existingRule) {
    try {
        New-NetFirewallRule -DisplayName $RULE_NAME `
            -Direction Inbound `
            -Protocol TCP `
            -LocalPort $SERVER_PORT `
            -Action Allow `
            -Profile Any `
            -Description "Permite acesso ao Sistema de Avaliação EN_430" | Out-Null
        Write-OK "Regra de firewall criada para porta $SERVER_PORT"
    } catch {
        Write-Warn "Não foi possível criar regra de firewall: $_"
        $AVISOS++
    }
} else {
    Write-OK "Regra de firewall já existe"
}

# =============================================================================
# 8. TESTAR CONFIGURAÇÃO E REINICIAR APACHE
# =============================================================================
Write-Step "[Teste] 8. Testando configuração e reiniciando Apache..."

$APACHE_EXE = Join-Path $APACHE_DIR "bin\httpd.exe"

# Testar configuração
Write-Host "  Testando configuração do Apache..."
$configTest = & $APACHE_EXE -t 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-OK "Configuração do Apache OK"
} else {
    Write-Error "Erro na configuração: $configTest"
    Write-Warn "  Verifique o arquivo $APACHE_CONF"
    $ERROS++
}

# Reiniciar Apache
Write-Host "  Reiniciando Apache..."
try {
    # Tentar restart primeiro
    & $APACHE_EXE -k restart 2>&1 | Out-Null
    if ($LASTEXITCODE -eq 0) {
        Write-OK "Apache reiniciado com sucesso"
    } else {
        # Fallback: stop + start
        & $APACHE_EXE -k stop 2>&1 | Out-Null
        Start-Sleep -Seconds 2
        & $APACHE_EXE -k start 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-OK "Apache iniciado com sucesso (stop+start)"
        } else {
            Write-Warn "Falha ao reiniciar Apache. Execute manualmente:"
            Write-Warn "  $APACHE_EXE -k restart"
            $AVISOS++
        }
    }
} catch {
    Write-Warn "Erro ao reiniciar Apache (tente manualmente): $_"
    $AVISOS++
}

# =============================================================================
# 9. TESTAR A APLICAÇÃO
# =============================================================================
Write-Step "[Teste] 9. Testando a aplicação..."

# Testar sintaxe PHP
Write-Host "  Validando sintaxe PHP..."
$testFiles = @("index.php", "functions.php", "config.php", "db.php")
foreach ($file in $testFiles) {
    $filePath = Join-Path $PHP_DEPLOY_DIR $file
    if (Test-Path $filePath) {
        $result = & php -l $filePath 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-OK "$file compilado sem erros"
        } else {
            Write-Warn "Erro em $file"
            $AVISOS++
        }
    }
}

# Testar servidor web
Write-Host "  Testando servidor web..."
Start-Sleep -Seconds 2

try {
    $response = Invoke-WebRequest -Uri "http://localhost/" -UseBasicParsing -TimeoutSec 5
    Write-OK "Landing page respondendo (HTTP $($response.StatusCode))"
} catch {
    Write-Warn "Landing page não respondeu"
    $AVISOS++
}

try {
    $response2 = Invoke-WebRequest -Uri "http://localhost/en430/sistema_avaliacao/php/" -UseBasicParsing -TimeoutSec 5
    Write-OK "Sistema de Avaliação respondendo (HTTP $($response2.StatusCode))"
} catch {
    Write-Warn "Sistema de Avaliação não respondeu"
}

# =============================================================================
# 10. GERAR SCRIPT DE CONTROLE
# =============================================================================
Write-Step "[Ferramentas] 10. Gerando script de controle..."

$CONTROL_SCRIPT = Join-Path $PHP_DEPLOY_DIR "controlar_servico.ps1"
$controlContent = @'
<#
.SYNOPSIS
    Controla o Apache HTTP Server no Windows
.DESCRIPTION
    Inicia, para ou reinicia o Apache + testa o sistema
.PARAMETER Action
    start | stop | restart | status | test
.EXAMPLE
    .\controlar_servico.ps1 start
    .\controlar_servico.ps1 restart
#>

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("start", "stop", "restart", "status", "test")]
    [string]$Action
)

# Detectar Apache
$APACHE_DIR = "C:\Apache24"
if (-not (Test-Path $APACHE_DIR)) { $APACHE_DIR = "C:\xampp\apache" }
$APACHE_EXE = Join-Path $APACHE_DIR "bin\httpd.exe"

$PHP_DIR = Split-Path -Parent $MyInvocation.MyCommand.Path

function Write-Status {
    param([string]$Text, [string]$Color)
    if ($Color -eq "Green") { $c = [ConsoleColor]::Green }
    elseif ($Color -eq "Red") { $c = [ConsoleColor]::Red }
    elseif ($Color -eq "Yellow") { $c = [ConsoleColor]::Yellow }
    else { $c = [ConsoleColor]::Cyan }
    Write-Host "$Text" -ForegroundColor $c
}

switch ($Action) {
    "start" {
        Write-Status "[Aguarde] Iniciando Apache..." "Cyan"
        Start-Process -FilePath $APACHE_EXE -ArgumentList "-k start" -WindowStyle Hidden -NoNewWindow
        Write-Status "[OK] Apache iniciado!" "Green"
        Write-Status "[Web] http://localhost/en430/" "Green"
        Write-Status "[Web] http://localhost/en430/sistema_avaliacao/php/" "Green"
    }
    "stop" {
        Write-Status "[Aguarde] Parando Apache..." "Cyan"
        Start-Process -FilePath $APACHE_EXE -ArgumentList "-k stop" -WindowStyle Hidden -NoNewWindow -Wait
        Write-Status "[OK] Apache parado!" "Green"
    }
    "restart" {
        Write-Status "[Aguarde] Reiniciando Apache..." "Cyan"
        Start-Process -FilePath $APACHE_EXE -ArgumentList "-k restart" -WindowStyle Hidden -NoNewWindow
        Write-Status "[OK] Apache reiniciado!" "Green"
    }
    "status" {
        $process = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
        if ($process) {
            Write-Status "[OK] Apache rodando ($($process.Count) processos)" "Green"
        } else {
            Write-Status "[ERRO] Apache não está rodando" "Red"
        }
    }
    "test" {
        Write-Status "[Teste] Validando PHP..." "Cyan"
        php -v
        Write-Status ""
        Write-Status "[Teste] Extensões PHP:" "Cyan"
        php -m | findstr pdo_sqlite
        Write-Status ""
        Write-Status "[Teste] Testando servidor web..." "Cyan"
        try { $r = Invoke-WebRequest -Uri "http://localhost/" -UseBasicParsing -TimeoutSec 3; Write-Status "[OK] Landpage: HTTP $($r.StatusCode)" "Green" } catch { Write-Status "[!] Landpage: offline" "Red" }
        try { $r2 = Invoke-WebRequest -Uri "http://localhost/en430/sistema_avaliacao/php/" -UseBasicParsing -TimeoutSec 3; Write-Status "[OK] Sistema: HTTP $($r2.StatusCode)" "Green" } catch { Write-Status "[!] Sistema: offline" "Red" }
        Write-Status ""
        Write-Status "[Teste] Banco de dados:" "Cyan"
        php -r "`$db = new PDO('sqlite:$PHP_DIR/avaliacao.db'); echo '   Questões: ' . `$db->query('SELECT COUNT(*) FROM questoes')->fetchColumn() . `"`n`";" 2>$null
    }
}
'@

# Ajustar caminho PHP_DIR no script
# Ajustar o caminho PHP_DIR no script de controle
$CONTROL_SCRIPT_CONTENT = $controlContent -replace 'C:\\php', ($PHP_DIR -replace '\\', '\\')

Set-Content -Path $CONTROL_SCRIPT -Value $CONTROL_SCRIPT_CONTENT
Write-OK "Script de controle criado: $CONTROL_SCRIPT"

# =============================================================================
# 11. RESUMO FINAL
# =============================================================================
Write-Step "[OK] 11. RESUMO DA INSTALAÇÃO"

Write-Host @"
+--------------------------------------------------------------------+
|                    INSTALAÇÃO CONCLUÍDA                             |
+--------------------------------------------------------------------+
"@ -ForegroundColor $GREEN

Write-Host "  [Pasta] Projeto:    $PROJECT_ROOT" -ForegroundColor $CYAN
Write-Host "  [Pasta] PHP:        $PHP_DIR" -ForegroundColor $CYAN
Write-Host "  [Apache] Apache:    $APACHE_DIR" -ForegroundColor $CYAN
Write-Host "  [DB]  Banco:      $DB_PATH" -ForegroundColor $CYAN
Write-Host "  [Web] URL:         http://localhost/en430/" -ForegroundColor $CYAN
Write-Host "  [Web] Avaliação:   http://localhost/en430/sistema_avaliacao/php/" -ForegroundColor $CYAN
Write-Host "  [Ferramentas] Controle: $CONTROL_SCRIPT" -ForegroundColor $CYAN
Write-Host ""

if ($AVISOS -gt 0) {
    Write-Host "  [!] $AVISOS aviso(s) - verifique os detalhes acima" -ForegroundColor $YELLOW
}

Write-Host @"

  [Info] PRÓXIMOS PASSOS:
  1. Abra o PowerShell como Administrador
  2. Execute: .\controlar_servico.ps1 start
  3. Acesse: http://localhost/en430/
  4. Sistema: http://localhost/en430/sistema_avaliacao/php/

  [Doc] Documentação completa em:
     GUIA_PUBLICACAO_APACHE_WINDOWS.md

"@ -ForegroundColor $WHITE

Write-Host "+--------------------------------------------------------------------+" -ForegroundColor $GREEN
Write-Host "`n[OK] Script concluído!" -ForegroundColor $GREEN
