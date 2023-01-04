#!/bin/bash

# Start server script for development use -> php, database server

clear
cd public/
sudo systemctl start mysql
sudo systemctl --no-pager status mysql
sudo php -S localhost:80