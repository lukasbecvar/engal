#!/bin/bash

# install frontend
cd ./frontend
sh scripts/install.sh

# install backend
cd ../backend
sh scripts/install.sh
