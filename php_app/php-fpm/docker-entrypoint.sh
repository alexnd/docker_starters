#!/usr/bin/env sh
set -eu

if [ ! -f /app/.env ]; then
cp -f /etc/php-app.env /app/.env
fi

exec "$@"