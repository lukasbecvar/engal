#!/bin/bash

clear

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# PHPSTAN analyze config
yellow_echo 'PHPSTAN: testing: ./config'
php vendor/bin/phpstan analyze --level 5 ./config

# PHPSTAN analyze index
yellow_echo 'PHPSTAN: testing: ./public/index.php'
php vendor/bin/phpstan analyze --level 5 ./public/index.php

# PHPSTAN analyze src
yellow_echo 'PHPSTAN: testing: ./src'
php vendor/bin/phpstan analyze --level 5 ./src

# PHPSTAN analyze tests
yellow_echo 'PHPSTAN: testing: ./tests'
php vendor/bin/phpstan analyze --level 5 ./tests

# PHPUnit run tests
yellow_echo 'PHPUnit: testing...'
php bin/phpunit --do-not-cache-result
