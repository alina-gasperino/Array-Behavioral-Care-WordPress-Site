#!/bin/bash

# This file exist to work around pipelines' requirement that build step 1 must be auto-triggered
cd "`dirname "$0"`/../..";
buildstamp=$(date '+%Y%j%H');
echo "$buildstamp" > .buildstamp;
echo "Build stamp: $buildstamp";

if [ "$BITBUCKET_DEPLOYMENT_ENVIRONMENT" == "Production" ]; then
    echo "Manually trigger the production deployment step";
fi;
