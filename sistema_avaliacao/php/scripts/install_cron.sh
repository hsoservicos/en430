#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# install_cron.sh — Instalador do Cron Job de Backup
# Sistema de Avaliação EN_430
# ═══════════════════════════════════════════════════════════════
#
# Uso:
#   sudo ./scripts/install_cron.sh                    # Instalar/atualizar cron job
#   sudo ./scripts/install_cron.sh --remove           # Remover cron job
#   sudo ./scripts/install_cron.sh --status           # Verificar status
#   ./scripts/install_cron.sh --dry-run               # Mostrar o que seria instalado
# ═══════════════════════════════════════════════════════════════

# Caminhos
PROJECT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
BACKUP_SCRIPT="$PROJECT_DIR/scripts/backup.sh"
CRON_LINE="0 3 * * * cd $PROJECT_DIR && bash $BACKUP_SCRIPT --auto > /dev/null 2>&1"
CRON_COMMENT="# Backup diario EN430 - $(date +%Y-%m-%d)"

# Cores
VERDE='\033[0;32m'
AMARELO='\033[1;33m'
VERMELHO='\033[0;31m'
AZUL='\033[0;34m'
NC='\033[0m' # Sem cor

info()  { echo -e "${AZUL}ℹ️${NC} $1"; }
ok()    { echo -e "${VERDE}✅${NC} $1"; }
warn()  { echo -e "${AMARELO}⚠️${NC} $1"; }
erro()  { echo -e "${VERMELHO}❌${NC} $1"; }

verificar_requisitos() {
    if ! command -v crontab &> /dev/null; then
        erro "crontab nao encontrado. Instale com: sudo apt install cron"
        exit 1
    fi
    
    if [ ! -f "$BACKUP_SCRIPT" ]; then
        erro "Script de backup nao encontrado: $BACKUP_SCRIPT"
        exit 1
    fi
    
    if [ ! -f "$PROJECT_DIR/avaliacao.db" ]; then
        warn "Banco de dados nao encontrado em $PROJECT_DIR/avaliacao.db"
        warn "O cron job sera instalado, mas o backup falhara ate o banco ser criado."
    fi
}

cron_instalar() {
    verificar_requisitos
    
    # Verificar se o cron job ja existe
    if crontab -l 2>/dev/null | grep -q "backup.sh.*--auto"; then
        warn "Cron job ja existe. Atualizando..."
        # Remover linha antiga
        (crontab -l 2>/dev/null | grep -v "backup.sh.*--auto") | crontab -
    fi
    
    # Adicionar nova linha
    (crontab -l 2>/dev/null; echo "$CRON_COMMENT"; echo "$CRON_LINE") | crontab -
    
    ok "Cron job instalado com sucesso!"
    echo ""
    echo "   Horario: Todos os dias as 03:00"
    echo "   Script:  $BACKUP_SCRIPT"
    echo "   Log:     /dev/null (silencioso)"
    echo ""
    
    # Dar permissao de execucao
    chmod +x "$BACKUP_SCRIPT"
    ok "Permissao de execucao garantida."
}

cron_remover() {
    if crontab -l 2>/dev/null | grep -q "backup.sh.*--auto"; then
        (crontab -l 2>/dev/null | grep -v "backup.sh.*--auto" | grep -v "$CRON_COMMENT") | crontab -
        ok "Cron job removido com sucesso!"
    else
        warn "Nenhum cron job do EN430 encontrado."
    fi
}

cron_status() {
    echo -e "${AZUL}📋${NC} Status do Cron Job - Backup EN430"
    echo ""
    
    if crontab -l 2>/dev/null | grep -q "backup.sh.*--auto"; then
        ok "Cron job esta instalado."
        echo ""
        echo "   Linha ativa:"
        crontab -l | grep "backup.sh" | head -1
        echo ""
        
        # Mostrar proxima execucao
        echo "   Proxima execucao: 03:00 (diario)"
    else
        warn "Cron job NAO esta instalado."
        echo ""
        echo "   Para instalar: sudo ./scripts/install_cron.sh"
    fi
    
    echo ""
    echo "   Diretorio: $PROJECT_DIR"
    
    # Verificar backups existentes
    if [ -d "$PROJECT_DIR/backups" ]; then
        local total=$(ls "$PROJECT_DIR/backups"/*.db.gz 2>/dev/null | wc -l)
        local size=$(du -sh "$PROJECT_DIR/backups" 2>/dev/null | cut -f1)
        echo "   Backups existentes: $total ($size)"
    else
        echo "   Backups existentes: nenhum"
    fi
}

# ═══════════════════════════════════════════════════════════════
# MAIN
# ═══════════════════════════════════════════════════════════════

case "${1:-}" in
    --remove)
        cron_remover
        ;;
    --status)
        cron_status
        ;;
    --dry-run)
        echo "📋 Dry-run: seria instalado:"
        echo "   $CRON_COMMENT"
        echo "   $CRON_LINE"
        echo ""
        echo "   Script de backup: $BACKUP_SCRIPT"
        echo "   Diretorio do projeto: $PROJECT_DIR"
        ;;
    *)
        cron_instalar
        ;;
esac
