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
# Passing MySQL password via command line arguments is insecure,
# so using `MYSQL_PWD` instead.
# ──────────────────────────────────────────────────────────────────────────────
set -a; source .env; set +a

export MYSQL_PWD="${MYSQL_ROOT_PASSWORD}"

# ──────────────────────────────────────────────────────────────────────────────
# Reset the database, through Docker containers.
# ──────────────────────────────────────────────────────────────────────────────
echo "// 🗑️ Dropping database..."
docker compose exec -e MYSQL_PWD db mysql -u root -e "DROP DATABASE IF EXISTS ${DATABASE_NAME};"

echo "// 🆕 Creating database..."
docker compose exec -e MYSQL_PWD db mysql -u root -e "CREATE DATABASE ${DATABASE_NAME};"

echo "// 📋 Loading schema.sql..."
docker compose exec -T -e MYSQL_PWD db mysql -u root $DATABASE_NAME < schema.sql

echo "   [OK] Database reset"
