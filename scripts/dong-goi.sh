#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
NAME="CSE703073_DT16_$(date +%Y%m%d_%H%M%S).zip"
zip -r "$NAME" . -x '.git/*' 'backend/vendor/*' 'frontend/node_modules/*' 'frontend/dist/*' 'backend/storage/logs/*' 'database/backup/*' 'database/raw/*' '*.env'
echo "$NAME"
