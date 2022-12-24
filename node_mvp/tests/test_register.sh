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

email=${args[2]}
if [ -z "${email}" ]; then
  echo Enter email:
  read email
fi

name=${args[3]}
if [ -z "${name}" ]; then
  echo Enter full name:
  read name
fi

curl -v -H 'content-type: application/json' -X POST \
  -d '{"username": "'$username'", "password": "'$password'", "email": "'$email'", "name":"'$name'"}' \
  http://127.0.0.1:$API_PORT/api/auth/register