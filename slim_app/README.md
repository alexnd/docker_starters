# Slim App

PHP Slim framework based app for full stack developers

## Install

1) `./install-docker.sh`

2) `docker-compose exec php ./install.sh`

3) `npm i`

4) `./build.sh`

5) Open [home page localhost:8000](http://localhost:8000) in web browser

## Structure

- app - Slim4 PHP code
- res - resources like html views, jsons, xmls, txts
- www - web server public root
- cdn - user contant files, mounted to root as /media
- tmp - temporrary storage buffer like session files, uploads
- vendor - created by Composer - 3d-party libraries

## Operation

./install-docker.sh - build and run project's Docker containers

./boot-app.sh - script for Docker entrypoint

./install.sh - install Composer dependencies

./reindex.sh - update PHP autoload with `composer dumpautoload`

./migrate.sh - load migrate.sql into DB

./dump.sh - save DB to db.dump.sql

./seed.sh - load db.sql to DB

./test.sh - run `php ./test.php`
