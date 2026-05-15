#!/usr/bin/env bash
set -e

git pull
npm run build
php artisan migrate --force
php artisan optimize
php artisan serve --host 0.0.0.0
