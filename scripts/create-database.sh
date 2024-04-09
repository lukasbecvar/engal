#!/bin/bash

cd ./backend

# create database
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:database:create --if-not-exists --env=test
