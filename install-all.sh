#!/bin/bash

# install backend modules
cd backend && sh scripts/install.sh

# install frontend modules
cd ../frontend && sh scripts/install.sh