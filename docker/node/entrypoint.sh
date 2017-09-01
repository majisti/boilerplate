#!/bin/bash
set -e

# gets the id from the folder, unless we override it with an env variable
user_id=${LOCAL_UID:-`stat -c %u /var/www/html`}

#create the user only if its greater than 1000
if (("$user_id" >= "1000")); then
    useradd --shell /bin/bash -u $user_id -o -c "" -m user
    export HOME=/home/user
fi

mkdir -p $HOME/.ssh

user_name=$(awk -F: "/:$user_id:/{print \$1}" /etc/passwd)

exec /usr/local/bin/gosu $user_name "$@"
