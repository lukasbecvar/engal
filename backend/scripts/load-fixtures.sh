#!/bin/bash

# drop database and migrate for create tables
sh scripts/drop-database.sh
sh scripts/migrate-database.sh

# load testing datafixtures
docker-compose run php bash -c "
    php bin/console doctrine:fixtures:load --no-interaction &&
    php bin/console doctrine:fixtures:load --no-interaction --env=test
"
