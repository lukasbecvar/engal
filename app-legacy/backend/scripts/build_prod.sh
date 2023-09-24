#!/bin/bash

# The API builder for create production file structure -> build/

clear # clear console after start script

#Color codes.
green_echo (){ echo "$(tput setaf 2)$1"; }
yellow_echo () { echo "$(tput setaf 3)$1"; }
red_echo () { echo "$(tput setaf 9)$1"; }
cecho () { echo "$(tput setaf 6)$1"; }


# delete old build if exist
if [ -d "build/" ] 
then
	sudo rm -r build/
fi

green_echo "Building production API..."

# build API
mkdir build/
cp -R app/ build/app/
cp -R public/ build/public/
cp -R scripts/ build/scripts/
cp composer.json build/
cp composer.phar build/
cp config.php build/
cp panel.sh build/

# print status msg
green_echo "API builded in build folder"
green_echo "Warning: Check config before upload on server!"