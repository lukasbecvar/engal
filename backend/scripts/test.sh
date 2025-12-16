#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# clear console
clear

yellow_echo "Starting tests..."

# run tests
docker-compose run php bash -c "
    php bin/console doctrine:fixtures:load --no-interaction --env=test &&
    php vendor/bin/phpcbf &&
    php vendor/bin/phpcs &&
    php vendor/bin/phpstan analyze &&
    php bin/phpunit
"
