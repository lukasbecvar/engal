#!/bin/bash

# load testing data
php ./bin/console doctrine:fixtures:load --no-interaction --purge-with-truncate
php ./bin/console doctrine:fixtures:load --no-interaction --purge-with-truncate --env=test
