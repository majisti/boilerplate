#!/bin/bash
set -e

user_id=`stat -c %u /var/www/html`

#we want to match the host's UID/GID
if (("$user_id" != "0")); then
    sed -ie "s/`id -u user`/$user_id/g" /etc/passwd
    export HOME=/home/user
#when docker is ran under windows, the files permissions are set to root (0) and therefore need to switch a few permissions
else
    chown -R user:user /home/user/.composer
fi

exec /usr/local/bin/gosu user "$@"
