#!/usr/bin/env bash
# File: /apps/monolith/bin/db-reset.sh
# ──────────────────────────────────────────────────────────────────────────────
# Database reset:
# * drops the database
# * then recreates it
# * and finally loads the schema
#
# Intended for development and testing purposes.
# ──────────────────────────────────────────────────────────────────────────────

_BIN_DIR="$(dirname "$(readlink -f "${BASH_SOURCE[0]:-$0}")")"
_ROOT_DIR="$(realpath "${_BIN_DIR}/..")"
cd "${_ROOT_DIR}"

# ──────────────────────────────────────────────────────────────────────────────
# Loading database config through environment variables.
# `set -a` enables exportation of env vars, while `set +a` disables it.
# Passing PostgreSQL password via command line arguments is insecure,
# so using `PGPASSWORD` instead.
# ──────────────────────────────────────────────────────────────────────────────
set -a; source .env; set +a

export PGPASSWORD="${DATABASE_PASSWORD}"

# ──────────────────────────────────────────────────────────────────────────────
# Reset the database, through Docker containers.
# ──────────────────────────────────────────────────────────────────────────────
echo '  // 🔌 Terminating active connections...'
echo ''
docker compose exec -e PGPASSWORD db psql \
    -U ${DATABASE_USER} \
    -d postgres \
    -c "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '${DATABASE_NAME}' AND pid <> pg_backend_pid();" \
    > /dev/null 2>&1

echo '  // 🗑️ Dropping database...'
echo ''
docker compose exec -e PGPASSWORD db psql \
    -U ${DATABASE_USER} \
    -d postgres \
    -c "DROP DATABASE IF EXISTS ${DATABASE_NAME};" \
    > /dev/null 2>&1

echo '  // 🆕 Creating database...'
echo ''
docker compose exec -e PGPASSWORD db psql \
    -U ${DATABASE_USER} \
    -d postgres \
    -c "CREATE DATABASE ${DATABASE_NAME};" \
    > /dev/null 2>&1

echo '  // 📋 Loading schema.sql...'
echo ''
docker compose exec -T -e PGPASSWORD db psql \
    -U ${DATABASE_USER} \
    -d ${DATABASE_NAME} \
    > /dev/null 2>&1 \
    < schema.sql

echo '  // 🔄 Restarting web container to clear connection pool...'
echo ''
docker compose restart web > /dev/null 2>&1

echo '  [OK] Database reset'
