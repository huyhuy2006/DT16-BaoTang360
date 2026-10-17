#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
cd backend
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
cd ../frontend
npm ci
npm run build
