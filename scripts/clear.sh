#!/bin/bash

red_echo () { echo "\033[31m\033[1m$1\033[0m"; }
 
# clear common env
red_echo "[Clear]: clearing common files"
sudo rm -rf _docker/services

# clear frontend
cd ./frontend
sh scripts/clear.sh

# clear backend
cd ../backend
sh scripts/clear.sh
