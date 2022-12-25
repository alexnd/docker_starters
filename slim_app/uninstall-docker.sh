#!/bin/bash

env $(cat ./_docker/env.ini) docker-compose stop && docker-compose down
