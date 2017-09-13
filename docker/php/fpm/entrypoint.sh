#!/bin/bash
set -e

: ${XDEBUG:=0}

# Change www-data's uid & guid to be the same as directory in host
sed -ie "s/`id -u www-data`/`stat -c %u /var/www/html`/g" /etc/passwd

# Disabled Xdebug if needed
if [ "$XDEBUG" = "0" ]; then
    rm -f /usr/local/etc/php/conf.d/xdebug.ini
fi

php-fpm -R