#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# clear console
clear

# clear old testing data
if [ -d './storage/test/' ]
then
    rm -rf ./storage/test
fi

yellow_echo "Starting tests..."

# run tests
docker-compose run php bash -c "
    php bin/console doctrine:fixtures:load --no-interaction --env=test &&
    php vendor/bin/phpcbf &&
    php vendor/bin/phpcs &&
    php vendor/bin/phpstan analyze &&
    php bin/phpunit
"
