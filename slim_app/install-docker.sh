#!/bin/bash

env $(cat ./_docker/env.ini) docker-compose up -d
