#!/bin/sh
set -eu
cd /var/www/html

if [ ! -d vendor ]; then
    composer install --no-interaction --prefer-dist --no-progress
fi

# Create an ignored local runtime file only if needed. Docker Compose supplies the
# real local credentials through environment variables from the root .env file.
if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi
if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --force
fi
php artisan storage:link || true

# Wait for MySQL. Never print the database password in logs.
connected=0
for i in $(seq 1 30); do
    if php artisan migrate --force; then connected=1; break; fi
    echo "Dang cho co so du lieu... ($i/30)"
    sleep 2
done
if [ "$connected" -ne 1 ]; then
    echo "Khong the chay migration. Kiem tra ket noi CSDL." >&2
    exit 1
fi

# Demo accounts use a documented local-only password. Never create them in production.
if [ "${APP_ENV:-local}" = "local" ] || [ "${APP_ENV:-local}" = "testing" ]; then
    USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -n 1 || true)
    if [ "$USER_COUNT" = "0" ]; then
        php artisan db:seed --class=DemoDataSeeder --force
    fi
else
    echo "APP_ENV is not local/testing; demo seeder intentionally skipped."
fi

exec "$@"
