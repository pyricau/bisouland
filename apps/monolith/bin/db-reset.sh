#!/usr/bin/env bash
# File: /apps/monolith/bin/db-reset.sh
# ──────────────────────────────────────────────────────────────────────────────
# Database reset:
# * drops the database
# * then recreates it
# * and finally loads the schema
#
# Usage:
#   ./bin/db-reset.sh                          # Reset database (wipes all data)
#   ./bin/db-reset.sh --keep-performance-metrics  # Reset database but preserve performance_metrics
#   APP_ENV=test ./bin/db-reset.sh              # Reset test database using .env.test
#
# Intended for development and testing purposes.
# ──────────────────────────────────────────────────────────────────────────────

KEEP_PERFORMANCE=false
if [ "$1" = "--keep-performance-metrics" ]; then
    KEEP_PERFORMANCE=true
fi

# Determine which .env file to use based on APP_ENV
APP_ENV="${APP_ENV:-prod}"
ENV_FILE=".env"
if [ "$APP_ENV" != "prod" ]; then
    ENV_FILE=".env.${APP_ENV}"
fi

_BIN_DIR="$(dirname "$(readlink -f "${BASH_SOURCE[0]:-$0}")")"
_ROOT_DIR="$(realpath "${_BIN_DIR}/..")"
cd "${_ROOT_DIR}"

# ──────────────────────────────────────────────────────────────────────────────
# Loading database config through environment variables.
# `set -a` enables exportation of env vars, while `set +a` disables it.
# Passing MySQL password via command line arguments is insecure,
# so using `MYSQL_PWD` instead.
# ──────────────────────────────────────────────────────────────────────────────
if [ ! -f "$ENV_FILE" ]; then
    echo "Error: $ENV_FILE not found"
    exit 1
fi

echo "// 📄 Loading ${ENV_FILE}..."
set -a; source "$ENV_FILE"; set +a

export MYSQL_PWD="${MYSQL_ROOT_PASSWORD}"

# ──────────────────────────────────────────────────────────────────────────────
# Reset the database, through Docker containers.
# ──────────────────────────────────────────────────────────────────────────────

# Backup performance metrics if requested
if [ "$KEEP_PERFORMANCE" = true ]; then
    echo "// 💾 Backing up performance_metrics..."
    docker compose exec -T -e MYSQL_PWD db mysqldump -u root --no-create-info ${DATABASE_NAME} performance_metrics > /tmp/performance_metrics_backup.sql 2>/dev/null || true
fi

echo "// 🗑️ Dropping database..."
docker compose exec -e MYSQL_PWD db mysql -u root -e "DROP DATABASE IF EXISTS ${DATABASE_NAME};"

echo "// 🆕 Creating database..."
docker compose exec -e MYSQL_PWD db mysql -u root -e "CREATE DATABASE ${DATABASE_NAME};"

echo "// 🔐 Granting permissions to ${DATABASE_USER}..."
docker compose exec -e MYSQL_PWD db mysql -u root -e "GRANT ALL PRIVILEGES ON ${DATABASE_NAME}.* TO '${DATABASE_USER}'@'%';"
docker compose exec -e MYSQL_PWD db mysql -u root -e "FLUSH PRIVILEGES;"

echo "// 📋 Loading schema.sql..."
# Replace hardcoded database name in schema.sql with the actual database name
sed "s/^USE skyswoon;/USE ${DATABASE_NAME};/" schema.sql | docker compose exec -T -e MYSQL_PWD db mysql -u root

# Restore performance metrics if backed up
if [ "$KEEP_PERFORMANCE" = true ] && [ -f /tmp/performance_metrics_backup.sql ]; then
    echo "// 🔄 Restoring performance_metrics..."
    docker compose exec -T -e MYSQL_PWD db mysql -u root $DATABASE_NAME < /tmp/performance_metrics_backup.sql
    rm -f /tmp/performance_metrics_backup.sql
    echo "   [OK] Database reset (performance metrics preserved)"
else
    echo "   [OK] Database reset"
fi
