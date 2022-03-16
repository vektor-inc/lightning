#!/bin/bash

cd _g3
rm -rf vendor
composer install --optimize-autoloader --prefer-dist --no-dev

cd ../
rm -rf vendor
composer install --optimize-autoloader --prefer-dist --no-dev
npm run build
npm run dist