#!/bin/bash

cd "`dirname "$0"`/../..";

sshuser='kfrymire';
sshhost='arraybc.com';

# pull wp-content dir from production to localhost
rsync -azv --delete \
    --exclude=debug.log \
    --exclude=plugins/athena-api-tool \
    --exclude=updraft \
    --exclude=upgrade \
    --exclude=var/cache \
    --exclude=wflogs \
    -e 'ssh -i ~kfrymire/.ssh/id_rsa' \
    "$sshuser@$sshhost:/var/www/arraybc-prod/wp-content/" wp-content;
