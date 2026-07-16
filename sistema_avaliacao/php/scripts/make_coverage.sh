#!/usr/bin/env bash
# ==============================================================================
#  make_coverage.sh — Geração de Relatório de Code Coverage
#
#  Sistema de Avaliação — Introdução à Enfermagem (EN_430)
#  PHP 8 + Apache + SQLite
#
#  Este script automatiza a geração de relatórios de cobertura de código:
#    1. Detecta/instala o Xdebug (via apt, pecl ou .deb)
#    2. Executa o PHPUnit com --coverage-html
#    3. Abre o relatório no navegador
#
#  Uso:
#    chmod +x scripts/make_coverage.sh
#    ./scripts/make_coverage.sh            # Gera relatório e abre navegador
#    ./scripts/make_coverage.sh --serve    # Gera + inicia servidor web para ver
#    ./scripts/make_coverage.sh --open     # Gera + tenta abrir no navegador
#    ./scripts/make_coverage.sh --only-install  # Só instala xdebug, não roda testes
#
#  Requer:
#    • PHP 8.1+
#    • Composer + PHPUnit instalados (vendor/bin/phpunit)
#    • Acesso à internet (para baixar xdebug se necessário)
#    • Opcional: xdg-open (Linux), open (macOS) para abrir navegador
#
# ==============================================================================

set -euo pipefail
shopt -s nullglob

# =============================================================================
# CORES
# =============================================================================
VERDE='\033[0;32m'
AMARELO='\033[1;33m'
VERMELHO='\033[0;31m'
CIANO='\033[0;36m'
RESET='\033[0m'

ok()    { echo -e "  ${VERDE}✅ ${1}${RESET}"; }
warn()  { echo -e "  ${AMARELO}⚠️  ${1}${RESET}"; }
erro()  { echo -e "  ${VERMELHO}❌ ${1}${RESET}"; }

# =============================================================================
# CONFIGURAÇÕES
# =============================================================================
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PHP_DIR="$(dirname "$SCRIPT_DIR")"
REPORTS_DIR="${PHP_DIR}/reports"
XDEBUG_SO="${PHP_DIR}/xdebug.so"

PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;')
PHP_API=$(php -r 'echo PHP_VERSION_ID;')
PHP_EXT_DIR=$(php -r 'echo ini_get("extension_dir");' 2>/dev/null || echo "")
PHP_INI_CLI=$(php -r 'echo php_ini_loaded_file() ?: "";' 2>/dev/null || echo "")

# URL do mirror Debian para xdebug
DEBIAN_MIRROR="http://ftp.br.debian.org/debian/pool/main/x/xdebug"

# =============================================================================
# FUNÇÕES
# =============================================================================

banner() {
    clear 2>/dev/null || true
    echo ""
    echo -e "${CIANO}══════════════════════════════════════════════════════════════════${RESET}"
    echo -e "${CIANO}║${RESET}                                                              ${CIANO}║${RESET}"
    echo -e "${CIANO}║${RESET}  📊 GERADOR DE CODE COVERAGE                                   ${CIANO}║${RESET}"
    echo -e "${CIANO}║${RESET}                                                              ${CIANO}║${RESET}"
    echo -e "${CIANO}║${RESET}  Sistema de Avaliação — Introdução à Enfermagem (EN_430)      ${CIANO}║${RESET}"
    echo -e "${CIANO}║${RESET}  PHP ${PHP_VERSION}                                           ${CIANO}║${RESET}"
    echo -e "${CIANO}║${RESET}  Relatório: reports/index.html                                ${CIANO}║${RESET}"
    echo -e "${CIANO}══════════════════════════════════════════════════════════════════${RESET}"
    echo ""
}

# ─── Passo 1: Verificar PHPUnit ────────────────────────────────────

verificar_phpunit() {
    echo -e "\n${CIANO}🔍 1. Verificando PHPUnit...${RESET}"

    if [[ -f "${PHP_DIR}/vendor/bin/phpunit" ]]; then
        PHPUNIT="${PHP_DIR}/vendor/bin/phpunit"
        local versao=$("$PHPUNIT" --version 2>/dev/null | head -1)
        ok "PHPUnit encontrado: $versao"
        return 0
    fi

    if command -v phpunit &>/dev/null; then
        PHPUNIT="phpunit"
        ok "PHPUnit global encontrado"
        return 0
    fi

    warn "PHPUnit não encontrado. Instale com Composer:"
    echo "    cd ${PHP_DIR} && php composer.phar install"
    echo ""
    echo "  Deseja instalar automaticamente? (s/N): "
    read -r choice
    if [[ "$choice" == "s" || "$choice" == "S" ]]; then
        instalar_composer
    else
        return 1
    fi
}

instalar_composer() {
    echo -e "\n${CIANO}📦 Instalando Composer e dependências...${RESET}"
    cd "$PHP_DIR"

    if [[ ! -f "composer.phar" ]]; then
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php composer-setup.php
        php -r "unlink('composer-setup.php');"
    fi

    php composer.phar install --no-interaction 2>&1 | tail -5

    if [[ -f "vendor/bin/phpunit" ]]; then
        PHPUNIT="vendor/bin/phpunit"
        ok "PHPUnit instalado com sucesso!"
    else
        erro "Falha ao instalar PHPUnit"
        return 1
    fi
}

# ─── Passo 2: Detectar/Instalar Xdebug ─────────────────────────

detectar_xdebug() {
    echo -e "\n${CIANO}🔍 2. Verificando Xdebug...${RESET}"

    # Testar se xdebug carrega
    if php -m 2>/dev/null | grep -q "xdebug"; then
        XDEBUG_ATIVO=true
        local versao=$(php -v 2>/dev/null | grep "with Xdebug" | head -1)
        ok "Xdebug já está ativo: ${versao:-versão desconhecida}"
        return 0
    fi

    # Procurar xdebug.so local
    if [[ -f "$XDEBUG_SO" ]]; then
        ok "xdebug.so encontrado localmente"

        # Testar carregamento
        if php -d "zend_extension=${XDEBUG_SO}" -m 2>/dev/null | grep -q "xdebug"; then
            XDEBUG_ATIVO=true
            ok "Xdebug carregado via zend_extension local"
            return 0
        fi
    fi

    # Verificar no diretório de extensões
    if [[ -n "$PHP_EXT_DIR" && -f "${PHP_EXT_DIR}/xdebug.so" ]]; then
        ok "xdebug.so encontrado no diretório de extensões"
        XDEBUG_SO="${PHP_EXT_DIR}/xdebug.so"

        if php -d "zend_extension=${XDEBUG_SO}" -m 2>/dev/null | grep -q "xdebug"; then
            XDEBUG_ATIVO=true
            ok "Xdebug pode ser carregado do diretório de extensões"
            return 0
        fi
    fi

    XDEBUG_ATIVO=false
    warn "Xdebug não encontrado. Tentando instalar..."
    return 1
}

instalar_xdebug() {
    echo -e "\n${CIANO}📥 3. Instalando Xdebug...${RESET}"

    # Método 1: apt (Linux)
    if command -v apt-get &>/dev/null; then
        echo -e "  ${CIANO}Tentando apt: php${PHP_VERSION}-xdebug...${RESET}"
        if sudo apt-get install -y -qq "php${PHP_VERSION}-xdebug" 2>/dev/null; then
            ok "Xdebug instalado via apt!"
            return 0
        fi

        # Tentar nome genérico
        if sudo apt-get install -y -qq php-xdebug 2>/dev/null; then
            ok "Xdebug instalado via apt (php-xdebug)!"
            return 0
        fi

        warn "apt não encontrou o pacote. Tentando pecl..."
    fi

    # Método 2: pecl
    if command -v pecl &>/dev/null; then
        echo -e "  ${CIANO}Tentando pecl install xdebug...${RESET}"
        if sudo pecl install xdebug 2>/dev/null; then
            ok "Xdebug instalado via pecl!"
            return 0
        fi
        warn "pecl falhou. Tentando download manual..."
    fi

    # Método 3: Download .deb manual (Debian/Ubuntu)
    if command -v curl &>/dev/null; then
        echo -e "  ${CIANO}Tentando download do .deb (Debian/Ubuntu)...${RESET}"
        cd /tmp

        # Listar versões disponíveis no mirror
        local disponiveis
        disponiveis=$(curl -sL "$DEBIAN_MIRROR" 2>/dev/null | grep -oP "php${PHP_VERSION}-xdebug_\d+\.\d+\.\d+-\d+_amd64\.deb" | head -5)

        if [[ -z "$disponiveis" ]]; then
            warn "Nenhuma versão encontrada para PHP ${PHP_VERSION} no mirror Debian"
        else
            local ultima=$(echo "$disponiveis" | tail -1)
            echo -e "  Baixando: ${ultima}..."

            if curl -sL "${DEBIAN_MIRROR}/${ultima}" -o xdebug.deb -w '%{http_code}' | grep -q "200"; then
                # Extrair .so do .deb
                mkdir -p /tmp/xdebug-extract
                cd /tmp/xdebug-extract
                ar x /tmp/xdebug.deb 2>/dev/null
                tar -xzf data.tar.gz 2>/dev/null || tar -xJf data.tar.xz 2>/dev/null || tar -xzf data.tar.zst 2>/dev/null || true

                # Procurar o .so
                local so_path
                so_path=$(find /tmp/xdebug-extract -name "xdebug.so" -type f 2>/dev/null | head -1)

                if [[ -n "$so_path" ]]; then
                    # Copiar para o diretório do projeto
                    cp "$so_path" "$XDEBUG_SO"
                    chmod 644 "$XDEBUG_SO"
                    ok "xdebug.so extraído com sucesso!"

                    # Limpar
                    rm -rf /tmp/xdebug-extract /tmp/xdebug.deb

                    # Testar
                    if php -d "zend_extension=${XDEBUG_SO}" -m 2>/dev/null | grep -q "xdebug"; then
                        XDEBUG_ATIVO=true
                        ok "Xdebug carregado e funcional!"
                        return 0
                    fi
                fi
            else
                warn "Download falhou para ${ultima}"
            fi
        fi
    fi

    erro "Não foi possível instalar o Xdebug automaticamente."
    echo ""
    echo "  Instale manualmente:"
    echo "    Linux (Debian/Ubuntu): sudo apt install php-xdebug"
    echo "    Linux (outros):        sudo pecl install xdebug"
    echo "    Windows:               Baixe o .dll de https://xdebug.org/download"
    echo ""
    echo "  Após instalar, ative no php.ini:"
    echo "    zend_extension=xdebug"
    echo "    xdebug.mode=coverage"
    echo ""
    return 1
}

# ─── Passo 3: Ativar XDEBUG_MODE ──────────────────────────────────

configurar_modo() {
    echo -e "\n${CIANO}⚙️  4. Configurando XDEBUG_MODE=coverage...${RESET}"

    export XDEBUG_MODE=coverage

    # Verificar se o modo foi aceito
    if php -d "zend_extension=${XDEBUG_SO}" -r "echo xdebug_info('mode') ?? 'coverage';" 2>/dev/null | grep -q "coverage"; then
        ok "XDEBUG_MODE=coverage configurado"
    else
        warn "Não foi possível verificar o modo — prosseguindo mesmo assim"
    fi
}

# ─── Passo 4: Executar PHPUnit com Coverage ───────────────────────

gerar_coverage() {
    echo -e "\n${CIANO}🧪 5. Executando PHPUnit com Code Coverage...${RESET}"

    # Garantir que o diretório reports existe
    mkdir -p "$REPORTS_DIR"

    cd "$PHP_DIR"

    local cmd="XDEBUG_MODE=coverage"

    # Adicionar zend_extension se necessário
    if [[ -f "$XDEBUG_SO" ]] && ! php -m 2>/dev/null | grep -q "xdebug"; then
        cmd="XDEBUG_MODE=coverage php -d zend_extension=${XDEBUG_SO} vendor/bin/phpunit --coverage-html=${REPORTS_DIR}"
    else
        cmd="XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html=${REPORTS_DIR}"
    fi

    echo -e "  Executando: ${CIANO}${cmd}${RESET}"
    echo ""

    # Executar com temporizador
    local start_time=$(date +%s)

    if eval "$cmd" 2>&1; then
        local end_time=$(date +%s)
        local elapsed=$((end_time - start_time))

        echo ""
        ok "Testes concluídos em ${elapsed}s!"
    else
        local exit_code=$?
        echo ""
        warn "PHPUnit retornou código ${exit_code} (alguns testes podem ter falhado)"
        echo "  O relatório parcial ainda foi gerado em: ${REPORTS_DIR}/index.html"
    fi
}

# ─── Passo 5: Mostrar Resultados ───────────────────────────────────

mostrar_resultados() {
    echo -e "\n${CIANO}📊 6. Relatório de Cobertura${RESET}"

    if [[ ! -f "${REPORTS_DIR}/index.html" ]]; then
        erro "Relatório não foi gerado!"
        return 1
    fi

    echo ""
    echo -e "  ${CIANO}📁 Relatório gerado em:${RESET}"
    echo "     ${REPORTS_DIR}/index.html"
    echo ""

    # Extrair métricas do HTML
    if command -v grep &>/dev/null; then
        local line_pct=$(grep -oP '(\d+\.\d+)% covered' "${REPORTS_DIR}/index.html" 2>/dev/null | head -1)
        local lines=$(grep -oP '(\d+)\s*/\s*(\d+)' "${REPORTS_DIR}/index.html" 2>/dev/null | head -1)
        local func_pct=$(grep -oP '(\d+\.\d+)% covered' "${REPORTS_DIR}/index.html" 2>/dev/null | tail -1)

        echo -e "  ${CIANO}📈 Métricas:${RESET}"
        echo -e "     Linhas:      ${AMARELO}${line_pct:-N/A}${RESET} (${lines:-N/A})"
        echo -e "     Funções:    ${AMARELO}${func_pct:-N/A}${RESET}"
    fi

    echo ""
    echo -e "  ${CIANO}📄 Arquivos analisados:${RESET}"
    for f in "${REPORTS_DIR}"/*.html; do
        local base=$(basename "$f")
        if [[ "$base" != "index.html" && "$base" != "dashboard.html" ]]; then
            local name=$(echo "$base" | sed 's/\.html$//')
            echo "     • ${name}.php  →  ${REPORTS_DIR}/${base}"
        fi
    done

    echo ""
}

# ─── Abrir no Navegador ────────────────────────────────────────────

abrir_navegador() {
    local report_path="${REPORTS_DIR}/index.html"

    if [[ ! -f "$report_path" ]]; then
        warn "Relatório não encontrado em $report_path"
        return 1
    fi

    echo -e "\n${CIANO}🌐 Abrindo relatório no navegador...${RESET}"

    if command -v xdg-open &>/dev/null; then
        xdg-open "$report_path" 2>/dev/null
        ok "Navegador aberto (xdg-open)"
    elif command -v open &>/dev/null; then
        open "$report_path" 2>/dev/null
        ok "Navegador aberto (open)"
    elif command -v start &>/dev/null; then
        start "$report_path" 2>/dev/null
        ok "Navegador aberto (start)"
    else
        warn "Não foi possível abrir o navegador automaticamente."
        echo "  Abra manualmente: file://${report_path}"
    fi
}

# ─── Servir via HTTP ──────────────────────────────────────────────

servir_http() {
    local port="${1:-8080}"

    if [[ ! -f "${REPORTS_DIR}/index.html" ]]; then
        erro "Relatório não encontrado. Execute o script sem --serve primeiro."
        return 1
    fi

    echo -e "\n${CIANO}🚀 Servindo relatório em http://127.0.0.1:${port}${RESET}"
    echo ""

    cd "$PHP_DIR"

    # Verificar se a porta já está em uso
    if command -v lsof &>/dev/null; then
        if lsof -ti:"${port}" &>/dev/null; then
            warn "Porta ${port} já está em uso. Tente: $0 --serve $((port + 1))"
            return 1
        fi
    fi

    # Iniciar servidor em background com nohup
    nohup php -S "127.0.0.1:${port}" -t reports/ > /tmp/php_coverage_server.log 2>&1 &
    disown

    # Aguardar servidor ficar pronto
    sleep 2
    if curl -s -o /dev/null -w '%{http_code}' "http://127.0.0.1:${port}/" 2>/dev/null | grep -q 200; then
        ok "Servidor HTTP iniciado em http://127.0.0.1:${port}/"
        echo -e "  ${AMARELO}⚠️  Para parar o servidor:${RESET}"
        echo "     kill \$(lsof -ti:${port})"
        echo ""
    else
        warn "Servidor pode não ter iniciado corretamente."
        echo "  Verifique: cat /tmp/php_coverage_server.log"
    fi
}

# ─── Limpeza ─────────────────────────────────────────────────────

limpar() {
    echo -e "\n${CIANO}🧹 Limpando artefatos temporários...${RESET}"

    if [[ -f "$XDEBUG_SO" ]]; then
        rm -f "$XDEBUG_SO"
        ok "xdebug.so removido"
    fi

    if [[ -d "$REPORTS_DIR" ]]; then
        rm -rf "$REPORTS_DIR"
        mkdir -p "$REPORTS_DIR"
        touch "$REPORTS_DIR/.gitkeep"
        ok "Relatórios limpos"
    fi

    ok "Limpeza concluída!"
}

# ─── Ajuda ─────────────────────────────────────────────────────────

mostrar_ajuda() {
    echo ""
    echo "  Uso: $0 [opções]"
    echo ""
    echo "  Opções:"
    echo "    --serve [porta]   Gera relatório e inicia servidor HTTP"
    echo "    --open            Gera relatório e abre no navegador"
    echo "    --only-install    Apenas instala/configura xdebug"
    echo "    --clean           Remove relatórios e xdebug.so"
    echo "    --help            Mostra esta ajuda"
    echo ""
    echo "  Exemplos:"
    echo "    $0                        # Gera relatório completo"
    echo "    $0 --open                 # Gera e abre no navegador"
    echo "    $0 --serve 8080           # Gera e serve em http://localhost:8080"
    echo "    $0 --only-install         # Só instala xdebug"
    echo "    $0 --clean                # Limpa tudo"
    echo ""

    # Mostrar status atual
    echo "  ─── Status Atual ──────────────────────────"
    if php -m 2>/dev/null | grep -q "xdebug"; then
        ok "Xdebug ativo"
    elif [[ -f "$XDEBUG_SO" ]]; then
        echo -e "  ${AMARELO}⚠️  xdebug.so presente mas não carregado${RESET}"
    else
        warn "Xdebug não encontrado"
    fi

    if [[ -f "${PHP_DIR}/vendor/bin/phpunit" ]]; then
        ok "PHPUnit disponível"
    else
        warn "PHPUnit não encontrado"
    fi

    if [[ -f "${REPORTS_DIR}/index.html" ]]; then
        ok "Relatório existente: reports/index.html"
    fi
    echo ""
}

# =============================================================================
# MAIN
# =============================================================================

# Parsear argumentos
SERVE=false
SERVE_PORT=8080
OPEN=false
ONLY_INSTALL=false
CLEAN=false

while [[ $# -gt 0 ]]; do
    case "$1" in
        --serve)
            SERVE=true
            SERVE_PORT="${2:-8080}"
            shift 2 2>/dev/null || shift
            ;;
        --open)
            OPEN=true
            shift
            ;;
        --only-install)
            ONLY_INSTALL=true
            shift
            ;;
        --clean)
            CLEAN=true
            shift
            ;;
        --help|-h)
            banner
            mostrar_ajuda
            exit 0
            ;;
        *)
            echo "Opção desconhecida: $1"
            echo "Use --help para ver as opções disponíveis."
            exit 1
            ;;
    esac
done

# ─── Modo Limpeza ──────────────────────────────────────────────────
if $CLEAN; then
    banner
    limpar
    exit 0
fi

# ─── Fluxo Principal ───────────────────────────────────────────────
banner

# 1. Verificar PHPUnit
if ! verificar_phpunit; then
    erro "PHPUnit é necessário para gerar coverage."
    echo "  Instale com: cd ${PHP_DIR} && php composer.phar install"
    exit 1
fi

# 2. Detectar/Instalar Xdebug
if ! detectar_xdebug; then
    if ! instalar_xdebug; then
        erro "Não foi possível obter o Xdebug. Verifique as instruções acima."
        exit 1
    fi
fi

# 3. Se for apenas instalar, parar aqui
if $ONLY_INSTALL; then
    echo ""
    ok "Xdebug configurado! Execute novamente sem --only-install para gerar o relatório."
    exit 0
fi

# 4. Configurar modo
configurar_modo

# 5. Gerar coverage
gerar_coverage

# 6. Mostrar resultados
mostrar_resultados

# 7. Abrir navegador ou servir HTTP
if $OPEN; then
    abrir_navegador
elif $SERVE; then
    servir_http "$SERVE_PORT" || true
fi

# ─── Aviso sobre xdebug.so ──────────────────────────────────
if [[ -f "$XDEBUG_SO" ]] && ! php -m 2>/dev/null | grep -q "xdebug"; then
    echo ""
    echo -e "  ${AMARELO}⚠️  xdebug.so está no diretório do projeto (${XDEBUG_SO})${RESET}"
    echo -e "  ${AMARELO}   Para removê-lo: rm -f ${XDEBUG_SO}${RESET}"
    echo ""
fi

# ─── Finalizar ─────────────────────────────────────────────────────
echo ""
echo -e "${VERDE}══════════════════════════════════════════════════════════════════${RESET}"
echo -e "${VERDE}║${RESET}                    ✅ PROCESSO CONCLUÍDO                        ${VERDE}║${RESET}"
echo -e "${VERDE}══════════════════════════════════════════════════════════════════${RESET}"
echo ""
echo -e "  📊 Relatório HTML: ${CIANO}file://${REPORTS_DIR}/index.html${RESET}"
echo ""
echo -e "  💡 Dicas:"
echo -e "     • ${CIANO}$0 --open${RESET}       → Gera e abre no navegador"
echo -e "     • ${CIANO}$0 --serve 8080${RESET} → Gera e serve em http://localhost:8080"
echo -e "     • ${CIANO}$0 --clean${RESET}      → Remove relatórios e xdebug.so"
echo ""

exit 0
