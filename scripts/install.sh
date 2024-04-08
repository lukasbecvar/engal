#!/bin/bash

# change working directory
cd ./frontend

# install node modules
if [ ! -d './node_modules/' ]
then
    npm install
fi
