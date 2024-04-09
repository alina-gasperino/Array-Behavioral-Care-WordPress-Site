#!/bin/bash

cd "`dirname "$0"`/../..";
here=$( pwd );

echo "###################################################"
echo "################ Starting CI build ################"
echo "###################################################"


# build config loader
echo " >>> Building config loader"
cd "$here/config";
if [ "$DOTENV" != "" ]; then
    echo "$DOTENV" | base64 --decode > ".env";
    cat ".env";
fi
composer install;
[ $? -eq 0 ] || ( echo "composer failed building config loader" > /dev/stderr && exit 4 );
echo " >>> done"


echo " >>> Building arraybc theme"
cd "$here/_arraybc-theme";
npm i && npm run prod;
[ $? -eq 0 ] || ( echo "build failed for arrabc theme" > /dev/stderr && exit 1 );
echo " >>> done"


echo " >>> Building athena-api-tool"
cd "$here/wp-content/plugins/athena-api-tool";
if [ "$DOTENV_ATHENA_PLUGIN" != "" ]; then
    echo "$DOTENV_ATHENA_PLUGIN" | base64 --decode > ".env";
fi

npm i && npm run prod;
[ $? -eq 0 ] || ( echo "npm failed building athena-api-tool" > /dev/stderr && exit 2 );

composer install && rm -rf var/cache/*;
[ $? -eq 0 ] || ( echo "composer failed building athena-api-tool" > /dev/stderr && exit 3 );
cd "$here";
echo " >>> done"

echo "############## Setting file permissions ##############"
chmod 0775 _src/bin/fix-paths.sh && _src/bin/fix-paths.sh;


date '+%Y%j%H' > .buildstamp;
echo "###################################################"
echo "################## CI build done ##################"
echo "###################################################"
echo ""

exit $result;
