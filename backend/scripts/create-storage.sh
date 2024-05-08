#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# create storage directory
if [ ! -d 'storage' ]
then
    yellow_echo "[Install]: creating storage dir"
    mkdir ./storage
fi

# set storage dir permissions
chmod -R 777 ./storage
