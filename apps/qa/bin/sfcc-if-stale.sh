#!/usr/bin/env bash
# File: /apps/qa/bin/sfcc-if-stale.sh
# ──────────────────────────────────────────────────────────────────────────────
# Symfony cache clear, but only if it's stale.
#
# Unlike dev, test and prod environments don't auto-invalidate cache when source
# files change. Changes to services, routes, Twig templates, Doctrine mappings,
# or environment variables all require a cache clear.
#
# This script detects stale cache by comparing modification times of src/,
# config/, and .env* files against the cache directory.
#
# Usage:
#
# ```shell
# bin/clear-cache-if-stale.sh
# bin/clear-cache-if-stale.sh prod
# ```
#
# Arguments:
#
# 1. `env`: Symfony environment, defaults to `test`
# ──────────────────────────────────────────────────────────────────────────────

_CLEAR_CACHE_ENV=${1:-test}
_CLEAR_CACHE_DIR="var/cache/${_CLEAR_CACHE_ENV}"

if [ ! -d "${_CLEAR_CACHE_DIR}" ]; then
    echo "  // Symfony cache directory does not exist, clearing..."
    php bin/console cache:clear --env="${_CLEAR_CACHE_ENV}"
    exit 0
fi

if [ -n "$(find src config .env* -newer "${_CLEAR_CACHE_DIR}" -print -quit 2>/dev/null)" ]; then
    echo "  // Symfony cache stale, clearing cache..."
    php bin/console cache:clear --env="${_CLEAR_CACHE_ENV}"
    exit 0
fi

echo "  // Symfony cache is up to date"
