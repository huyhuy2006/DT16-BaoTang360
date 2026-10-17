#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
python - <<'PY'
import csv
from pathlib import Path
root=Path('database/raw/dt16_300_records.csv')
with root.open(encoding='utf-8-sig') as f:
    n=sum(1 for _ in csv.DictReader(f))
assert n==300, n
print('[OK] CSV records:',n)
PY
php_tests=$(grep -Rho 'function test_[A-Za-z0-9_]*' backend/tests --include='*.php' | wc -l)
pw_tests=$(grep -Rho 'test(' tests/e2e --include='*.ts' | wc -l)
[ "$php_tests" -eq 20 ] && [ "$pw_tests" -eq 40 ]
echo "[OK] PHPUnit: $php_tests; Playwright: $pw_tests; total: $((php_tests+pw_tests))"
find backend/storage/app/public -type f | grep -E '\.(jpg|jpeg|png|wav|mp3)$' >/dev/null
test -f docs/wireframes/01-home.svg
echo '[OK] Media and wireframes present'
