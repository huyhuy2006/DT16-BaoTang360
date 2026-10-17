#!/usr/bin/env bash
set -euo pipefail
mkdir -p database/backup
TS=$(date +%Y%m%d_%H%M%S)
if command -v mysqldump >/dev/null 2>&1; then mysqldump -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" -u "${DB_USERNAME:-root}" -p"${DB_PASSWORD:-}" "${DB_DATABASE:-baotang_ao}" > "database/backup/baotang_ao_${TS}.sql"; else echo 'mysqldump not found'; exit 1; fi
