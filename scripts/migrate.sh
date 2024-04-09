#!/bin/bash

cd ./backend

# create database
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:database:create --if-not-exists --env=test

# delete old migrations
rm -rf migrations

# create new migrations directory
mkdir migrations

# database migration for update database structure
php bin/console make:migration --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction --env=test
