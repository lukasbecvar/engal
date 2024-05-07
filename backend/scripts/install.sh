#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

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

# set storage dir permissions
chmod -R 777 ./storage
