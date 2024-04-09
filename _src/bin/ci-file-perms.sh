#!/bin/bash

if [ $(id -ur) -ne 0 ]; then
    echo "WARNING: fix-paths.sh running without root privileges";
fi;

cd "$(dirname "$0")/../.."; #go to base directory
here=$( pwd );

automaton=1004
wwwdev=1001

echo "Clearing scrap files...";
find . -type f \( -name '.DS_Store' -or -name '._*' \) -delete;
rm -Rf $(find . -type d -name '.AppleDouble');

mkdir -p wp-content/uploads wp-content/plugins

echo "Setting file permissions on $here...";
find . -type d -exec chmod 0775 {} \; ;
find . -type f -exec chmod 0664 {} \; ;

echo "Setting script executable permissions...";
chmod 0775 vendor/bin/*

echo "Setting owner automaton:www-dev ($automaton:$wwwdev) on $here...";
chown -Rf $automaton:$wwwdev .

echo "Done";
