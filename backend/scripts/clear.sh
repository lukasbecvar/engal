#!/bin/bash

red_echo () { echo "\033[31m\033[1m$1\033[0m"; }

# clear env data
red_echo "[Clear]: clearing backend files"
php bin/console cache:clear
php bin/console cache:pool:clear --all

# delete public bundles data
rm -rf public/bundles

# delete var
sudo rm -rf var/

# delete composer data
rm -rf vendor/
rm -rf composer.lock

# delete jwt key
rm -rf config/jwt
