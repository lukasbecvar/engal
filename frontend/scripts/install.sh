#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# install node-modules
if [ ! -d './node_modules/' ]
then
    yellow_echo "[Install]: installing node_modules (frontend)"
    docker-compose run frontend npm install
fi
