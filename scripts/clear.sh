#!/bin/bash

# clear
red_echo () { echo "\033[31m\033[1m$1\033[0m"; }

# clear docker
sudo rm -rf docker/services
red_echo "[Clear]: common files clear OK"

# clear frontend
cd ./frontend
rm -rf build
sudo rm -rf node_modules
rm -rf package-lock.json
red_echo "[Clear]: frontend files clear OK"

# clear backend
cd ../backend
php bin/console cache:clear
rm -rf var/
rm -rf vendor/
rm -rf composer.lock
rm -rf config/jwt
red_echo "[Clear]: backend files clear OK"
