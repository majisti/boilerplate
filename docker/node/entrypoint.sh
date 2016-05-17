#!/bin/bash
set -e

# Change www-data's uid & guid to be the same as directory in host or the configured one
sed -ie "s/`id -u www-data`/`stat -c %u /var/www/html`/g" /etc/passwd
chown -R www-data /var/www

# Execute all commands with user www-data
su www-data -s /bin/bash -c "$*"
