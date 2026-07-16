#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# backup.sh — Script de Backup do Banco de Dados SQLite
# Sistema de Avaliação EN_430
# ═══════════════════════════════════════════════════════════════
#
# Uso:
#   ./scripts/backup.sh                   # Backup manual
#   ./scripts/backup.sh --auto            # Backup automático (silencioso)
#   ./scripts/backup.sh --list            # Listar backups existentes
#   ./scripts/backup.sh --restore <file>  # Restaurar backup
#   ./scripts/backup.sh --clean           # Apagar backups antigos (>30 dias)
#
# Agendar no crontab (Linux):
#   0 3 * * * /var/www/enfermagem/sistema_avaliacao/php/scripts/backup.sh --auto
#
# Agendar no Windows (Agendador de Tarefas):
#   C:\xampp\php\php.exe C:\xampp\htdocs\en430\sistema_avaliacao\php\scripts\backup.php
# ═══════════════════════════════════════════════════════════════

# Configurações
BACKUP_DIR="$(dirname "$0")/../backups"
DB_PATH="$(dirname "$0")/../avaliacao.db"
DATE=$(date +%Y%m%d_%H%M%S)
MAX_BACKUP_DAYS=30

# Criar diretório de backup se não existir
mkdir -p "$BACKUP_DIR"

# Funções
do_backup() {
    local file="$BACKUP_DIR/avaliacao_$DATE.db"
    
    # Verificar se o banco existe
    if [ ! -f "$DB_PATH" ]; then
        echo "❌ ERRO: Banco de dados não encontrado em $DB_PATH"
        exit 1
    fi
    
    # Verificar integridade do banco antes do backup
    INTEGRITY=$(php -r "
        try {
            \$db = new PDO('sqlite:$DB_PATH');
            \$result = \$db->query('PRAGMA integrity_check')->fetchColumn();
            echo \$result;
        } catch (Exception \$e) {
            echo 'FAIL';
        }
    ")
    
    if [ "$INTEGRITY" != "ok" ]; then
        echo "❌ ERRO: Banco de dados corrompido! Backup cancelado."
        echo "   Execute: php -r \"\\\$db = new PDO('sqlite:$DB_PATH'); echo \\\$db->query('PRAGMA integrity_check')->fetchColumn();\""
        exit 1
    fi
    
    # Realizar backup via VACUUM INTO (cópia consistente)
    php -r "
        \$db = new PDO('sqlite:$DB_PATH');
        \$db->exec('VACUUM INTO \"$file\"');
    " 2>/dev/null
    
    if [ $? -eq 0 ] && [ -f "$file" ]; then
        # Comprimir
        gzip -f "$file"
        local size=$(du -h "${file}.gz" | cut -f1)
        
        if [ "${1:-}" != "--auto" ]; then
            echo "✅ Backup concluído: ${file}.gz (${size})"
        fi
        
        # Manter apenas backups dos últimos MAX_BACKUP_DAYS dias
        find "$BACKUP_DIR" -name "avaliacao_*.db.gz" -mtime +$MAX_BACKUP_DAYS -delete 2>/dev/null
        
        return 0
    else
        echo "❌ ERRO: Falha ao criar backup!"
        return 1
    fi
}

case "${1:-}" in
    --auto)
        do_backup --auto > /dev/null 2>&1
        ;;
    --list)
        echo "📋 Backups disponíveis:"
        echo ""
        if [ "$(ls -A "$BACKUP_DIR" 2>/dev/null)" ]; then
            ls -lhS "$BACKUP_DIR"/*.db.gz 2>/dev/null | awk '{
                split($9,parts,"/");
                file=parts[length(parts)];
                split(file,info,"_");
                printf "  📦 %s (%s) - %s\n", file, $5, info[2] ? info[2] : "?"
            }'
            echo ""
            local total=$(ls "$BACKUP_DIR"/*.db.gz 2>/dev/null | wc -l)
            local size=$(du -sh "$BACKUP_DIR" 2>/dev/null | cut -f1)
            echo "   Total: $total backup(s) — $size"
        else
            echo "   Nenhum backup encontrado."
        fi
        ;;
    --restore)
        if [ -z "${2:-}" ]; then
            echo "❌ Uso: $0 --restore <arquivo.db.gz>"
            exit 1
        fi
        local restore_file="$BACKUP_DIR/$2"
        if [ ! -f "$restore_file" ]; then
            echo "❌ Arquivo não encontrado: $restore_file"
            echo "   Use $0 --list para ver os backups disponíveis."
            exit 1
        fi
        echo "⚠️  ATENÇÃO: Isso irá SUBSTITUIR o banco de dados atual!"
        echo "   Origem: $restore_file"
        echo "   Destino: $DB_PATH"
        echo ""
        read -p "   Digite 'RESTAURAR' para confirmar: " confirm
        if [ "$confirm" = "RESTAURAR" ]; then
            # Fazer backup automático antes de restaurar
            do_backup --auto
            # Descomprimir e restaurar
            gunzip -c "$restore_file" > "$DB_PATH"
            echo "✅ Banco restaurado com sucesso de: $2"
        else
            echo "❌ Restauração cancelada."
            exit 1
        fi
        ;;
    --clean)
        local removed=$(find "$BACKUP_DIR" -name "avaliacao_*.db.gz" -mtime +$MAX_BACKUP_DAYS -delete -print 2>/dev/null | wc -l)
        echo "🧹 Removidos $removed backup(s) com mais de $MAX_BACKUP_DAYS dias."
        ;;
    *)
        do_backup
        ;;
esac
