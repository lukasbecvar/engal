#!/bin/bash

red_echo () { echo "\033[31m\033[1m$1\033[0m"; }

red_echo "[Clear]: clearing frontend files"
rm -rf ./build
sudo rm -rf ./node_modules
rm ./package-lock.json
