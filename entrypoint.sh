#!/usr/bin/env bash
cp -r -n /var/www/omeka-s/. /var/www/html/demo/
rm -r /var/www/omeka-s
chown -R www-data:www-data /var/www/html

exec "$@"