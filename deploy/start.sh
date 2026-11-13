#!/bin/sh
set -eu
mkdir -p /var/data/uploads/profile_pictures
# Seed only on first deployment. Never overwrite the live SQLite database.
if [ ! -f /var/data/ptjpm.sqlite ]; then
    cp /var/www/html/data/ptjpm.sqlite /var/data/ptjpm.sqlite
fi
if [ ! -f /var/data/.uploads-seeded ]; then
    cp -an /var/www/html/uploads/. /var/data/uploads/
    touch /var/data/.uploads-seeded
fi
if [ ! -L /var/www/html/uploads ]; then
    rm -rf /var/www/html/uploads
    ln -s /var/data/uploads /var/www/html/uploads
fi
chown -R www-data:www-data /var/data
exec apache2-foreground
