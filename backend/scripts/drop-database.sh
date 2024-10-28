#!/bin/bash

yellow_echo () { echo "\033[33m\033[1m$1\033[0m"; }

# drop database
yellow_echo "[DB-Drop]: delete databases..."
docker-compose run php bash -c "
    php bin/console doctrine:database:drop --force &&
    php bin/console doctrine:database:drop --env=test --force
"

# delete media files
yellow_echo "[DB-Drop]: delete media files..."
rm -r ./storage
