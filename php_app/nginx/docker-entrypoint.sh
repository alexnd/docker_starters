#!/usr/bin/env sh
set -eu

envsubst '${WORK_DIR} ${PHPFPM_PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

exec "$@"