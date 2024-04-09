#!/bin/bash

# clear terminal history
clear

# test frontend
cd ./frontend
# run jest tests
npx jest

# test backend
cd ../backend
php vendor/bin/phpstan
php bin/phpunit
