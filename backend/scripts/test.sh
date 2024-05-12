#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# clear console
clear

# static code analyze
yellow_echo 'STATIC-ANALYZE: testing...'
sh ./scripts/static-analyze.sh

# PHPUnit tests
yellow_echo 'PHPUnit: testing...'
php ./bin/phpunit
