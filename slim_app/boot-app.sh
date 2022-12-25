#!/bin/bash

ENV_FILE_SRC=/etc/php-app.env
ENV_FILE_APP=/app/.env

if [ ! -f "$ENV_FILE_APP" ]; then
  if [ ! -f "$ENV_FILE_SRC" ]; then
    echo env configuration source $ENV_FILE_SRC not found
  else
    cp -f $ENV_FILE_SRC $ENV_FILE_APP
  fi
fi

chmod 0644 /etc/cron.d/crontab && crontab /etc/cron.d/crontab
