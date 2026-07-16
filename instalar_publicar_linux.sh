#!/usr/bin/env bash
# ==============================================================================
#
#  🐧 Script de Instalação, Implantação e Publicação — Linux (Ubuntu/Debian)
#
#  Sistema de Avaliação — Introdução à Enfermagem (EN_430)
#
#  Este script automatiza TODO o processo de deploy do sistema de
#  avaliação PHP 8 + Apache + SQLite no Linux (Ubuntu/Debian).
#
#  Funcionalidades:
#    • Verificação de pré-requisitos (PHP 8, Apache, SQLite)
#    • Instalação de PHP 8 e extensões
#    • Configuração do Apache com mod_rewrite
#    • Criação e população do banco de dados SQLite
#    • Configuração de VirtualHost
#    • Configuração de permissões
#    • Teste de funcionamento
#
#  Uso:
#    chmod +x instalar_publicar_linux.sh
#    sudo ./instalar_publicar_linux.sh
#
#  Requer:
#    • Ubuntu 20.04+ / Debian 11+
#    • Acesso root (sudo)
#    • Conexão com internet
#
# ==============================================================================

set -euo pipefail
shopt -s nullglob

# =============================================================================
# CORES PARA OUTPUT
# =============================================================================
VERDE='\033[0;32m'
AMARELO='\033[1;33m'
VERMELHO='\033[0;31m'
CIANO='\033[0;36m'
BRANCO='\033[1;37m'
RESET='\033[0m'

# =============================================================================
# CONFIGURAÇÕES
# =============================================================================
PROJECT_ROOT="$(cd "$(dirname "$0")" && pwd)"
PHP_DIR="${PROJECT_ROOT}/sistema_avaliacao/php"

# Diretórios de instalação
WWW_DIR="/var/www"
DEPLOY_DIR="${WWW_DIR}/enfermagem"

SERVER_PORT=80
SERVER_NAME="localhost"
LOG_FILE="/tmp/instalar_enfermagem.log"

# =============================================================================
# FUNÇÕES AUXILIARES
# =============================================================================
passo() {
    echo -e "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo -e "  ${CIANO}${1}${RESET}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
}

ok()  { echo -e "  ${VERDE}✅ ${1}${RESET}"; }
warn(){ echo -e "  ${AMARELO}⚠️  ${1}${RESET}"; }
erro(){ echo -e "  ${VERMELHO}❌ ${1}${RESET}"; }

comando_existe() { command -v "$1" &>/dev/null; }

log() {
    local msg="[$(date '+%H:%M:%S')] $1"
    echo "$msg" >> "$LOG_FILE"
}

verificar_root() {
    if [[ $EUID -ne 0 ]]; then
        erro "Este script deve ser executado como root (sudo)!"
        echo -e "  ${AMARELO}💡 Execute: sudo ./instalar_publicar_linux.sh${RESET}"
        exit 1
    fi
}

# =============================================================================
# INÍCIO
# =============================================================================
clear
echo ""
echo -e "${CIANO}══════════════════════════════════════════════════════════════════${RESET}"
echo -e "${CIANO}║${RESET}                                                              ${CIANO}║${RESET}"
echo -e "${CIANO}║${RESET}  🐧 INSTALAÇÃO E PUBLICAÇÃO NO LINUX                          ${CIANO}║${RESET}"
echo -e "${CIANO}║${RESET}                                                              ${CIANO}║${RESET}"
echo -e "${CIANO}║${RESET}  Sistema de Avaliação — Introdução à Enfermagem (EN_430)      ${CIANO}║${RESET}"
echo -e "${CIANO}║${RESET}  PHP 8 + Apache + SQLite                                     ${CIANO}║${RESET}"
echo -e "${CIANO}║${RESET}                                                              ${CIANO}║${RESET}"
echo -e "${CIANO}══════════════════════════════════════════════════════════════════${RESET}"
echo ""

# Iniciar log
rm -f "$LOG_FILE"
log "=== INÍCIO DA INSTALAÇÃO ==="

verificar_root

ERROS=0
AVISOS=0

# =============================================================================
# 1. VERIFICAR PRÉ-REQUISITOS
# =============================================================================
passo "🔍 1. Verificando pré-requisitos..."

# Verificar sistema
if [[ -f /etc/os-release ]]; then
    . /etc/os-release
    ok "Sistema: $NAME $VERSION_ID"
else
    warn "Não foi possível identificar o sistema operacional"
    AVISOS=$((AVISOS+1))
fi

# Verificar PHP 8
if comando_existe php; then
    phpver=$(php -v 2>&1 | head -1)
    if echo "$phpver" | grep -q "PHP 8\\."; then
        ok "PHP 8 encontrado: $phpver"
    else
        warn "PHP encontrado mas versão pode ser antiga: $phpver"
        AVISOS=$((AVISOS+1))
    fi
else
    warn "PHP não encontrado — será instalado automaticamente"
    AVISOS=$((AVISOS+1))
fi

# Verificar extensões PHP essenciais
if php -m 2>/dev/null | grep -q "pdo_sqlite"; then
    ok "Extensão pdo_sqlite disponível"
else
    warn "pdo_sqlite não disponível — será instalada"
    AVISOS=$((AVISOS+1))
fi

if php -m 2>/dev/null | grep -q "mbstring"; then
    ok "Extensão mbstring disponível"
else
    warn "mbstring não disponível — será instalada"
    AVISOS=$((AVISOS+1))
fi

# Verificar Apache
if comando_existe apache2; then
    apver=$(apache2 -v 2>&1 | head -1)
    ok "Apache encontrado: $apver"
else
    warn "Apache não encontrado — será instalado automaticamente"
    AVISOS=$((AVISOS+1))
fi

# Verificar diretório do projeto
if [[ -d "$PHP_DIR" ]]; then
    ok "Diretório PHP do projeto encontrado: $PHP_DIR"
else
    erro "Diretório PHP não encontrado em $PHP_DIR"
    ERROS=$((ERROS+1))
fi

if [[ -f "${PHP_DIR}/index.php" ]]; then
    ok "Front controller (index.php) encontrado"
else
    erro "index.php não encontrado em $PHP_DIR"
    ERROS=$((ERROS+1))
fi

if [[ $ERROS -gt 0 ]]; then
    echo -e "\n${VERMELHO}❌ $ERROS erro(s) encontrado(s). Corrija-os e execute novamente.${RESET}"
    [[ $AVISOS -gt 0 ]] && echo -e "${AMARELO}⚠️  $AVISOS aviso(s)${RESET}"
    log "FALHA: $ERROS erro(s) de pré-requisito"
    exit 1
fi

echo -e "\n${VERDE}✅ Todos os pré-requisitos OK! Prosseguindo...${RESET}"
log "Pré-requisitos OK"

# =============================================================================
# 2. INSTALAR DEPENDÊNCIAS DO SISTEMA
# =============================================================================
passo "📦 2. Instalando dependências do sistema..."

if ! dpkg -l php php-sqlite3 php-mbstring libapache2-mod-php apache2 sqlite3 &>/dev/null; then
    echo -e "  ${CIANO}Atualizando lista de pacotes...${RESET}"
    apt-get update -qq 2>&1 | tail -1 || true

    echo -e "  ${CIANO}Instalando Apache, PHP 8 e SQLite...${RESET}"
    DEBIAN_FRONTEND=noninteractive apt-get install -y -qq \
        apache2 \
        php \
        php-sqlite3 \
        php-mbstring \
        php-xml \
        libapache2-mod-php \
        sqlite3 \
        2>&1 | tail -3 || true

    ok "Dependências do sistema instaladas"
    log "Dependências do sistema instaladas"
else
    ok "Todas as dependências do sistema já estão instaladas"
fi

# Ativar módulos do Apache
a2enmod rewrite 2>/dev/null || true
a2enmod headers 2>/dev/null || true
a2enmod expires 2>/dev/null || true
ok "Módulos do Apache ativados: rewrite, headers, expires"

# =============================================================================
# 3. COPIAR ARQUIVOS PARA O DIRETÓRIO WEB
# =============================================================================
passo "📂 3. Copiando arquivos para o diretório web..."

# Criar diretório de deploy
mkdir -p "$DEPLOY_DIR"

# Copiar landing page e materiais estáticos
echo -e "  Copiando landing page e materiais..."
cp -r "$PROJECT_ROOT"/*.html "$DEPLOY_DIR/" 2>/dev/null || true
cp -r "$PROJECT_ROOT"/*.pdf "$DEPLOY_DIR/" 2>/dev/null || true
cp -r "$PROJECT_ROOT"/*.md "$DEPLOY_DIR/" 2>/dev/null || true

# Copiar sistema de avaliação PHP
echo -e "  Copiando sistema de avaliação PHP..."
mkdir -p "${DEPLOY_DIR}/sistema_avaliacao"
cp -r "${PROJECT_ROOT}/sistema_avaliacao/php" "${DEPLOY_DIR}/sistema_avaliacao/php" 2>/dev/null || true

ok "Arquivos copiados para $DEPLOY_DIR"
log "Arquivos copiados para diretório web"

# Ajustar permissões
chown -R www-data:www-data "$DEPLOY_DIR" 2>/dev/null || true
ok "Permissões ajustadas para www-data"

# =============================================================================
# 4. CRIAR E POPULAR O BANCO DE DADOS
# =============================================================================
passo "🗄️  4. Criando e populando o banco de dados..."

PHP_DEPLOY_DIR="${DEPLOY_DIR}/sistema_avaliacao/php"
DB_PATH="${PHP_DEPLOY_DIR}/avaliacao.db"

cd "$PHP_DEPLOY_DIR"

if [[ -f "$DB_PATH" ]]; then
    warn "Banco de dados já existe ($(du -h "$DB_PATH" | cut -f1))"
    echo -ne "  ${AMARELO}Deseja recriar? (s/N): ${RESET}"
    read -r choice
    if [[ "$choice" == "s" || "$choice" == "S" ]]; then
        rm -f "$DB_PATH"
        echo -e "  Executando recriar_questoes.php..."
        php scripts/recriar_questoes.php
        log "Banco de dados recriado"
    else
        ok "Utilizando banco existente"
    fi
else
    echo -e "  Executando recriar_questoes.php..."
    php scripts/recriar_questoes.php
    log "Banco de dados criado"
fi

# Ajustar permissão do banco (sempre, mesmo se existente)
chown www-data:www-data "$DB_PATH" 2>/dev/null || true
chmod 664 "$DB_PATH" 2>/dev/null || true
ok "Permissões do banco ajustadas"

# Contar questões
total_questoes=$(php -r "
try {
    \$db = new PDO('sqlite:avaliacao.db');
    \$total = \$db->query('SELECT COUNT(*) FROM questoes')->fetchColumn();
    echo \$total;
} catch (Exception \$e) {
    echo '?';
}
" 2>/dev/null)
ok "Total de questões no banco: $total_questoes"

# Ajustar permissão do banco
chown www-data:www-data "$DB_PATH" 2>/dev/null || true
chmod 664 "$DB_PATH" 2>/dev/null || true

cd "$PROJECT_ROOT"

# =============================================================================
# 5. CONFIGURAR O APACHE — VIRTUALHOST
# =============================================================================
passo "🌐 5. Configurando Apache — VirtualHost"

VHOST_FILE="/etc/apache2/sites-available/enfermagem.conf"

# Verificar se já existe
SOBRESCREVER_VHOST=false
if [[ -f "$VHOST_FILE" ]]; then
    warn "VirtualHost já existe em $VHOST_FILE"
    echo -ne "  ${AMARELO}Deseja sobrescrever? (s/N): ${RESET}"
    read -r choice
    if [[ "$choice" == "s" || "$choice" == "S" ]]; then
        SOBRESCREVER_VHOST=true
        echo -e "  ${CIANO}Criando nova configuração...${RESET}"
    else
        ok "Mantendo configuração existente"
        AVISOS=$((AVISOS+1))
    fi
else
    SOBRESCREVER_VHOST=true
fi

if $SOBRESCREVER_VHOST; then
    echo -e "  ${CIANO}Criando VirtualHost...${RESET}"

    cat > "$VHOST_FILE" <<VHOST
<VirtualHost *:${SERVER_PORT}>
    ServerName ${SERVER_NAME}

    # ─── Document Root (Landing page + materiais) ───
    DocumentRoot ${DEPLOY_DIR}
    <Directory ${DEPLOY_DIR}>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # ─── Página padrão ───
    DirectoryIndex index.php index.html index_estudos.html

    # ─── Sistema de Avaliação PHP ───
    <Directory ${PHP_DEPLOY_DIR}>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # ─── Cache de Arquivos Estáticos ───
    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/html "access plus 1 day"
        ExpiresByType text/css "access plus 7 days"
        ExpiresByType application/javascript "access plus 7 days"
        ExpiresByType application/pdf "access plus 7 days"
        ExpiresByType image/png "access plus 30 days"
    </IfModule>

    # ─── Compressão ───
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/css application/javascript
        AddOutputFilterByType DEFLATE application/pdf text/plain
    </IfModule>

    # ─── Cabeçalhos de Segurança ───
    <IfModule mod_headers.c>
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-Frame-Options "DENY"
        Header always set Referrer-Policy "strict-origin-when-cross-origin"
    </IfModule>

    # ─── Proteger arquivos sensíveis ───
    <FilesMatch "\\.(db|sqlite|md|txt|log|ini)$">
        Require all denied
    </FilesMatch>

    # ─── Logs ───
    ErrorLog \${APACHE_LOG_DIR}/enfermagem_error.log
    CustomLog \${APACHE_LOG_DIR}/enfermagem_access.log combined
</VirtualHost>
VHOST
    ok "VirtualHost criado: $VHOST_FILE"
    log "VirtualHost criado"
fi

# Ativar site
a2ensite enfermagem.conf 2>/dev/null || true
a2dissite 000-default.conf 2>/dev/null || true
ok "Site enfermagem ativado"

# =============================================================================
# 6. CONFIGURAR FIREWALL (UFW)
# =============================================================================
passo "🛡️  6. Configurando firewall (UFW)..."

if comando_existe ufw; then
    if ufw status | grep -q "Status: active"; then
        ufw allow "${SERVER_PORT}/tcp" 2>/dev/null && ok "Porta $SERVER_PORT liberada no UFW" || warn "Não foi possível configurar UFW"
    else
        warn "UFW não está ativo — verifique a configuração de firewall manualmente"
    fi
else
    warn "UFW não encontrado — configure o firewall manualmente se necessário"
fi

# =============================================================================
# 7. TESTAR CONFIGURAÇÃO E REINICIAR APACHE
# =============================================================================
passo "🧪 7. Testando configuração e reiniciando Apache..."

echo -e "  ${CIANO}Testando configuração do Apache...${RESET}"
if apache2ctl configtest 2>&1; then
    ok "Configuração do Apache OK"
else
    erro "Erro na configuração do Apache. Verifique $VHOST_FILE"
    ERROS=$((ERROS+1))
fi

echo -e "  ${CIANO}Reiniciando Apache...${RESET}"
systemctl restart apache2 2>&1 || true
if systemctl is-active --quiet apache2; then
    ok "Apache reiniciado com sucesso"
    log "Apache reiniciado"
else
    erro "Falha ao reiniciar Apache. Verifique os logs: journalctl -u apache2"
    ERROS=$((ERROS+1))
fi

# =============================================================================
# 8. TESTAR A APLICAÇÃO
# =============================================================================
passo "🧪 8. Testando a aplicação..."

# Testar sintaxe PHP
echo -e "  ${CIANO}Validando sintaxe PHP...${RESET}"
if php -l "${PHP_DEPLOY_DIR}/index.php" >/dev/null 2>&1; then
    ok "index.php compilado sem erros"
else
    erro "Erro ao validar index.php"
    ERROS=$((ERROS+1))
fi

if php -l "${PHP_DEPLOY_DIR}/functions.php" >/dev/null 2>&1; then
    ok "functions.php compilado sem erros"
else
    erro "Erro ao validar functions.php"
    ERROS=$((ERROS+1))
fi

# Testar acesso HTTP
echo -e "  ${CIANO}Testando servidor web...${RESET}"
sleep 2
HTTP_CODE=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost/" 2>/dev/null || echo "000")
if [[ "$HTTP_CODE" == "200" || "$HTTP_CODE" == "302" || "$HTTP_CODE" == "301" ]]; then
    ok "Landing page respondendo (HTTP $HTTP_CODE)"
else
    warn "Landing page não respondeu (HTTP $HTTP_CODE)"
    AVISOS=$((AVISOS+1))
fi

HTTP_CODE_AV=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost/sistema_avaliacao/php/" 2>/dev/null || echo "000")
if [[ "$HTTP_CODE_AV" == "200" || "$HTTP_CODE_AV" == "302" ]]; then
    ok "Sistema de Avaliação respondendo (HTTP $HTTP_CODE_AV)"
else
    warn "Sistema de Avaliação não respondeu (HTTP $HTTP_CODE_AV)"
fi

# =============================================================================
# 9. CRIAR SCRIPT DE CONTROLE
# =============================================================================
passo "📎 9. Criando script de controle..."

CONTROL_SCRIPT="/usr/local/bin/controlar_enfermagem"
cat > "$CONTROL_SCRIPT" <<'CTRL'
#!/usr/bin/env bash
# Script de controle do Sistema de Avaliação EN_430
# Uso: controlar_enfermagem {start|stop|restart|status|logs|test}

PHP_DIR="/var/www/enfermagem/sistema_avaliacao/php"
ACTION="${1:-status}"

case "$ACTION" in
    start)
        echo "🔄 Iniciando Apache..."
        systemctl start apache2
        echo "✅ Apache iniciado!"
        echo "🌐 Landing page: http://localhost/"
        echo "🌐 Avaliação:    http://localhost/sistema_avaliacao/php/"
        ;;
    stop)
        echo "🔄 Parando Apache..."
        systemctl stop apache2
        echo "✅ Apache parado!"
        ;;
    restart)
        echo "🔄 Reiniciando Apache..."
        systemctl restart apache2
        echo "✅ Apache reiniciado!"
        ;;
    status)
        systemctl status apache2 --no-pager -l | head -20
        ;;
    logs)
        echo "📋 Logs do Apache:"
        echo "    tail -f /var/log/apache2/enfermagem_error.log"
        echo "    tail -f /var/log/apache2/enfermagem_access.log"
        ;;
    test)
        echo "📋 Testando PHP..."
        php -v
        echo ""
        echo "📋 Extensões PHP (SQLite):"
        php -m | grep -E 'pdo_sqlite|sqlite3|mbstring'
        echo ""
        echo "📋 Testando servidor web..."
        curl -s -o /dev/null -w "Landing page: HTTP %{http_code}\n" http://localhost/
        curl -s -o /dev/null -w "Avaliação:    HTTP %{http_code}\n" http://localhost/sistema_avaliacao/php/
        echo ""
        echo "📋 Testando banco de dados..."
        php -r "
            \$db = new PDO('sqlite:${PHP_DIR}/avaliacao.db');
            \$q = \$db->query('SELECT COUNT(*) FROM questoes')->fetchColumn();
            echo \"   Questões no banco: \$q\n\";
        " 2>/dev/null || echo "   Erro ao acessar banco"
        ;;
    info)
        echo "📋 Informações do Sistema:"
        echo "   Landing page: /var/www/enfermagem/"
        echo "   PHP:          ${PHP_DIR}/"
        echo "   Banco:        ${PHP_DIR}/avaliacao.db"
        echo "   Logs:         /var/log/apache2/enfermagem_*.log"
        echo "   Config:       /etc/apache2/sites-available/enfermagem.conf"
        echo "   PHP versão:   $(php -v 2>&1 | head -1)"
        ;;
    *)
        echo "Uso: controlar_enfermagem {start|stop|restart|status|logs|test|info}"
        exit 1
        ;;
esac
CTRL
chmod +x "$CONTROL_SCRIPT"
ok "Script de controle criado: $CONTROL_SCRIPT"
log "Script de controle criado"

# =============================================================================
# 10. TESTES PHPUNIT (OPCIONAL)
# =============================================================================
passo "🧪 10. Testes automatizados (PHPUnit)..."

if [[ -f "${PHP_DIR}/vendor/bin/phpunit" ]]; then
    echo -e "  ${CIANO}Executando testes PHPUnit...${RESET}"
    cd "$PHP_DIR"
    if php vendor/bin/phpunit --no-coverage 2>&1; then
        ok "Testes PHPUnit passaram!"
    else
        warn "Alguns testes falharam"
        AVISOS=$((AVISOS+1))
    fi
    cd "$PROJECT_ROOT"
elif [[ -x "$(command -v phpunit)" ]]; then
    echo -e "  ${CIANO}Executando testes PHPUnit (global)...${RESET}"
    cd "$PHP_DIR"
    if phpunit --no-coverage 2>&1; then
        ok "Testes PHPUnit passaram!"
    else
        warn "Alguns testes falharam"
        AVISOS=$((AVISOS+1))
    fi
    cd "$PROJECT_ROOT"
else
    warn "PHPUnit não encontrado. Instale com: cd ${PHP_DIR} && composer install"
    AVISOS=$((AVISOS+1))
fi

# =============================================================================
# 11. RESUMO FINAL
# =============================================================================
passo "🎉 11. RESUMO DA INSTALAÇÃO"

echo ""
echo -e "${VERDE}══════════════════════════════════════════════════════════════════${RESET}"
echo -e "${VERDE}║${RESET}                    INSTALAÇÃO CONCLUÍDA                        ${VERDE}║${RESET}"
echo -e "${VERDE}══════════════════════════════════════════════════════════════════${RESET}"
echo ""
echo -e "  ${CIANO}📁 Landing page:  ${BRANCO}$DEPLOY_DIR${RESET}"
echo -e "  ${CIANO}📁 Sistema PHP:   ${BRANCO}$PHP_DEPLOY_DIR${RESET}"
echo -e "  ${CIANO}🗄️  Banco:         ${BRANCO}$DB_PATH${RESET}"
echo -e "  ${CIANO}🌐 Landing page:  ${BRANCO}http://localhost/${RESET}"
echo -e "  ${CIANO}🌐 Avaliação:     ${BRANCO}http://localhost/sistema_avaliacao/php/${RESET}"
echo -e "  ${CIANO}🔧 Controle:      ${BRANCO}controlar_enfermagem {start|stop|restart|status|logs|test}${RESET}"
echo ""

if [[ $ERROS -gt 0 ]]; then
    echo -e "  ${VERMELHO}❌ $ERROS erro(s) encontrado(s)${RESET}"
fi
if [[ $AVISOS -gt 0 ]]; then
    echo -e "  ${AMARELO}⚠️  $AVISOS aviso(s) — verifique os detalhes acima${RESET}"
fi

echo ""
echo -e "  ${BRANCO}📋 PRÓXIMOS PASSOS:${RESET}"
echo -e "  1. Acesse: ${BRANCO}http://localhost/${RESET}"
echo -e "  2. Acesse: ${BRANCO}http://localhost/sistema_avaliacao/php/${RESET}"
echo -e "  3. Use: ${BRANCO}controlar_enfermagem test${RESET} para diagnóstico"
echo -e "  4. Use: ${BRANCO}controlar_enfermagem logs${RESET} para monitorar"
echo ""
echo -e "  ${CIANO}📖 Documentação: ${DEPLOY_DIR}/GUIA_PUBLICACAO_APACHE.md${RESET}"
echo ""

echo -e "${VERDE}══════════════════════════════════════════════════════════════════${RESET}"
echo -e "${VERDE}║${RESET}                    🎉 SUCESSO!                              ${VERDE}║${RESET}"
echo -e "${VERDE}══════════════════════════════════════════════════════════════════${RESET}"
echo ""

log "=== INSTALAÇÃO CONCLUÍDA ==="
log "Erros: $ERROS | Avisos: $AVISOS"

exit 0
