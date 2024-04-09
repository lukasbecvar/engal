#!/bin/bash

# clear docker
sudo rm -rf docker/services

# clear frontend
cd ./frontend
rm -rf build
sudo rm -rf node_modules
rm -rf package-lock.json

# clear backend
cd ../backend
php bin/console cache:clear
rm -rf var/
rm -rf vendor/
rm -rf composer.lock
rm -rf config/jwt
