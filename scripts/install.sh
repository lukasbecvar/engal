#!/bin/bash

# colored print
yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# install frontend
cd ./frontend
# install node-modules
if [ ! -d './node_modules/' ]
then
    yellow_echo "[Install]: installing node_modules (frontend)"
    npm install
fi

# install backend
cd ../backend
# install composer
if [ ! -d './vendor/' ]
then
    yellow_echo "[Install]: installing composer vendor (backend)"
    composer install
fi
# generate jwk key
if [ ! -d 'config/jwt/' ]
then
    yellow_echo "[Install]: generating new jwt keypair"
    php bin/console lexik:jwt:generate-keypair
fi
