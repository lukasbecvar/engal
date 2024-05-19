#!/bin/bash

# install & build assets
sh scripts/install.sh

# build docker containers
sudo docker-compose up --build
