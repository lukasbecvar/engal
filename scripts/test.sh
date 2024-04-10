#!/bin/bash

# clear terminal history
clear

# test frontend
cd ./frontend
sh scripts/test.sh

# test backend
cd ../backend
sh scripts/test.sh