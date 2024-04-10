#!/bin/bash

# clear
red_echo () { echo "\033[31m\033[1m$1\033[0m"; }

# clear common env
red_echo "[Clear]: clearing common files"
sudo rm -rf _docker/services

# clear frontend
red_echo "[Clear]: clearing frontend files"
cd ./frontend
rm -rf build
sudo rm -rf node_modules
rm -rf package-lock.json

# clear backend
red_echo "[Clear]: clearing backend files"
cd ../backend
php bin/console cache:clear
rm -rf public/bundles
rm -rf var/
rm -rf vendor/
rm -rf composer.lock
rm -rf config/jwt
