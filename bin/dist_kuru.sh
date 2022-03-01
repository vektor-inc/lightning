#!/bin/bash

rm -r vendor
composer install --optimize-autoloader --prefer-dist --no-dev
npm run build
npx gulp dist
windowszip dist/lightning