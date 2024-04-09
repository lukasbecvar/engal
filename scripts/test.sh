#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# clear terminal history
clear

# test frontend
cd ./frontend
yellow_echo "[Test]: testing Jest"
# run jest tests
npx jest

# test backend
cd ../backend
yellow_echo "[Test]: testing PHPStan"
php vendor/bin/phpstan
yellow_echo "[Test]: testing PHPUnit"
php bin/phpunit
