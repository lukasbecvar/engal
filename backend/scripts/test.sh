#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# load testing data fixtures
yellow_echo "[Test]: execute data fixtures (test env)"
php bin/console doctrine:fixtures:load --no-interaction --env=test

# test phpstan
yellow_echo "[Test]: testing PHPStan"
php vendor/bin/phpstan

# test phpunit
yellow_echo "[Test]: testing PHPUnit"
php bin/phpunit
