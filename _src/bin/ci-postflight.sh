#!/bin/bash

cd "`dirname "$0"`/../..";

echo "###########################################################"
echo "################ CI postflight $(hostname) ################"
echo "###########################################################"

mkdir -vp config logs;
chmod -Rf ug+rw config logs;

php -r 'opcache_reset();'
composer dump-autoload --optimize --ignore-platform-reqs;  # needed in case cache references deleted class files


echo "################################################################"
echo "################ CI postflight done $(hostname) ################"
echo "################################################################"
echo ""

exit 0
