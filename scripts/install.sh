#!/bin/bash

# install frontend
cd ./frontend
# install node-modules
if [ ! -d './node_modules/' ]
then
    npm install
fi

# install backend
cd ../backend
# install composer
if [ ! -d './vendor/' ]
then
    composer install
fi
# generate jwk key
if [ ! -d 'config/jwt/' ]
then
    php bin/console lexik:jwt:generate-keypair
fi
