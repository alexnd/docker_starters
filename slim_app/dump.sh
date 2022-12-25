#!/bin/bash

unamestr=$(uname)
if [ "$unamestr" = 'Linux' ]; then
  export $(grep -v '^#' .env | xargs -d '\n')
elif [[ "$unamestr" = 'FreeBSD' || "$unamestr" = 'Darwin' ]]; then
  export $(grep -v '^#' .env | xargs -0)
fi

mysqladmin -u$DB_USERNAME -p$DB_PASSWORD --host=$DB_HOST --port=$DB_PORT $DB_DATABASE > ./db.dump.sql
