#!/bin/bash

echo "=== Clearing config cache ==="
php artisan config:clear

echo "=== Running migrations ==="
php artisan migrate --force || echo "Migration failed"

echo "=== Starting Laravel server ==="
php artisan serve --host=0.0.0.0 --port=8080