#!/usr/bin/env sh
set -eu

if [ ! -f /app/.env ]; then
    cp -f /etc/app.api.env /app/.env
    #cp -f /app/env.template /app/.env
    #sed -i "s/API_PORT=/API_PORT=$API_PORT/" /app/.env
fi

exec "$@"