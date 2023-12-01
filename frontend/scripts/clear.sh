#!/bin/bash

rm -rf node_modules/
rm -rf package-lock.json

if [ -d 'build/' ]
then
    rm -rf build/
fi
