#!/bin/bash

# analyze phpstan
php ./vendor/bin/phpstan analyze

# analyze coding standards
php ./vendor/bin/phpcs
