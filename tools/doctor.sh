#!/usr/bin/env bash
set -u
check(){ command -v "$1" >/dev/null 2>&1 && echo "[OK] $1: $($2 2>&1 | head -n1)" || echo "[THIEU] $1"; }
check git "git --version"; check php "php -v"; check composer "composer --version"; check mysql "mysql --version"; check node "node -v"; check python3 "python3 --version"; check docker "docker --version"
for ext in pdo_mysql mbstring openssl fileinfo curl zip intl gd; do php -m 2>/dev/null | grep -qi "^$ext$" && echo "[OK] PHP $ext" || echo "[THIEU] PHP $ext"; done
