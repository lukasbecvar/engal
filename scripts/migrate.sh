#!/bin/bash

cd ./backend

# delete old migrations
rm -rf migrations

# create new migrations directory
mkdir migrations

# database migration for update database structure
php bin/console make:migration --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction --env=test
