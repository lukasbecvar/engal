#!/bin/bash

# navigate to backend
cd ./backend

# load testing data
php bin/console doctrine:fixtures:load --no-interaction
php bin/console doctrine:fixtures:load --no-interaction --env=test
