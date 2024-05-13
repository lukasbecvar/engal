#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# clear console
clear

# load testing data fixtures
php ./bin/console doctrine:fixtures:load --no-interaction --purge-with-truncate --env=test

# static code analyze
yellow_echo 'STATIC-ANALYZE: testing...'
sh ./scripts/static-analyze.sh

# PHPUnit tests
yellow_echo 'PHPUnit: testing...'
php ./bin/phpunit
