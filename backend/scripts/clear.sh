#!/bin/bash

red_echo () { echo "\033[31m\033[1m$1\033[0m"; }

red_echo "[Clear]: clearing backend files"
php bin/console cache:clear
rm -rf public/bundles
rm -rf var/
rm -rf vendor/
rm -rf composer.lock
rm -rf config/jwt
