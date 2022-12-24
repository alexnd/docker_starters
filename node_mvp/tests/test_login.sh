#!/bin/bash

unamestr=$(uname)
if [ "$unamestr" = 'Linux' ]; then
  export $(grep -v '^#' .env | xargs -d '\n')
elif [[ "$unamestr" = 'FreeBSD' || "$unamestr" = 'Darwin' ]]; then
  export $(grep -v '^#' .env | xargs -0)
fi

args=("$@")

username=${args[0]}
if [ -z "${username}" ]; then
  echo Enter username:
  read username
fi

password=${args[1]}
if [ -z "${password}" ]; then
  echo Enter password:
  read password
fi

curl -v -H 'content-type: application/json' -X POST \
  -d '{"username":"'$username'", "password": "'$password'"}' \
  http://127.0.0.1:$API_PORT/api/auth/login