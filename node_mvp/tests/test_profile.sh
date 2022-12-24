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

username=${@: -2}
if [ -z "${username}" ]; then
  echo Enter username:
  read username
fi

if [ -z "${username}" ]; then
  curl -v -H 'content-type: application/json' \
    -H "Authorization: Bearer $token" \
    GET http://127.0.0.1:$API_PORT/api/auth/user
else
  curl -v -H 'content-type: application/json' \
    -H "Authorization: Bearer $token" \
    GET http://127.0.0.1:$API_PORT/api/auth/user/$username
fi
