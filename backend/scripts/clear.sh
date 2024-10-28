#!/bin/bash

# delete public bundles data
sudo rm -rf ./public/bundles

# delete var
sudo sudo rm -rf ./var

# delete composer data
sudo rm -rf ./vendor
sudo rm ./composer.lock

# delete jwt key
sudo rm -rf ./config/jwt
