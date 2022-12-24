#!/bin/bash

unamestr=$(uname)
if [ "$unamestr" = 'Linux' ]; then
  export $(grep -v '^#' .env | xargs -d '\n')
elif [[ "$unamestr" = 'FreeBSD' || "$unamestr" = 'Darwin' ]]; then
  export $(grep -v '^#' .env | xargs -0)
fi

args=("$@")

token=${args[0]}
if [ -z "${token}" ]; then
  echo Enter token:
  read token
fi

username=${args[1]}
if [ -z "${username}" ]; then
  echo Enter username:
  read username
fi

password=${args[2]}
if [ -z "${password}" ]; then
  echo Enter password:
  read password
fi

email=${args[3]}
if [ -z "${email}" ]; then
  echo Enter email:
  read email
fi

name=${args[4]}
if [ -z "${name}" ]; then
  echo Enter full name:
  read name
fi

curl -v -H 'content-type: application/json' \
  -H "Authorization: Bearer $token" -X POST \
  -d '{"username": "'$username'", "password": "'$password'", "email": "'$email'", "name":"'$name'"}' \
  http://127.0.0.1:$API_PORT/api/auth/user