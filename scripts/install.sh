#!/bin/bash

# install backend
cd ./backend
sh scripts/install.sh

# install frontend
cd ../frontend
sh scripts/install.sh
