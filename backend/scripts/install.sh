#!/bin/bash

# install all application requirements
composer install

# delete templates/ after installation (not used twig folder)
rm -rf templates/