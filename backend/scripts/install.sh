#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# install composer
if [ ! -d './vendor/' ]
then
    yellow_echo "[Install]: installing composer vendor (backend)"
    docker-compose run composer install --ignore-platform-reqs
fi

# generate jwk key
if [ ! -d 'config/jwt/' ]
then
    yellow_echo "[Install]: generating new jwt keypair"
    docker-compose run php ./bin/console lexik:jwt:generate-keypair
fi

# run storage create
sh ./scripts/create-storage.sh
