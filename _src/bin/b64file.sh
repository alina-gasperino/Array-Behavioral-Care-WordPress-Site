#!/bin/bash

cd "`dirname "$0"`/../..";

envfile="config/.env";

if [ "$1" != '' -a -f "$1" ]; then
    envfile="$1";
else
    . $envfile;
    envfile="$envfile.$APP_ENV";
fi;


fname=$( basename $envfile );

if [ $( which pbcopy ) != '' ]; then
    base64 --break=0 -i "$envfile" | tee /dev/stderr | pbcopy
    echo "$fname copied to clipboard"
else
    base64 --wrap=0 "$envfile";
    echo "$fname"
fi
