#!/bin/bash

unamestr=$(uname)
if [ "$unamestr" = 'Linux' ]; then
  export $(grep -v '^#' .env | xargs -d '\n')
elif [[ "$unamestr" = 'FreeBSD' || "$unamestr" = 'Darwin' ]]; then
  export $(grep -v '^#' .env | xargs -0)
fi

token=${@: -1}
if [ -z "${token}" ]; then
  echo Enter token:
  read token
fi

curl -v -H 'content-type: application/json' \
  -H "Authorization: Bearer $token" \
  GET http://127.0.0.1:$API_PORT/api/auth