#!/bin/sh
# entrypoint.sh — inicializa o banco SQLite e sobe o Apache
set -e

DB_FILE="${APP_DB_PATH:-/var/www/html/avaliacao.db}"
DB_DIR="$(dirname "$DB_FILE")"

mkdir -p "$DB_DIR"
chown -R www-data:www-data "$DB_DIR"

if [ ! -s "$DB_FILE" ]; then
    echo "[entrypoint] Banco ausente em $DB_FILE — executando migração inicial..."
    su -s /bin/sh www-data -c "php /var/www/html/scripts/migrate.php" \
        || echo "[entrypoint] AVISO: migração falhou; a aplicação subirá sem questões."
else
    echo "[entrypoint] Banco existente em $DB_FILE — migração ignorada."
fi

exec "$@"
