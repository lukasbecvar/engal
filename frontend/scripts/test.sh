#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# run jest tests
yellow_echo "[Test]: testing Jest"
npx jest
