#!/bin/bash

if [ $(id -ur) -ne 0 ]; then
    echo "CAUTION: fix-paths.sh is running without root privileges";
fi;

cd "$(dirname "$0")/../.."; #go to base directory

echo "Applying standard permissions to `pwd`...";

find . -type f \( -name '.DS_Store' -or -name '._*' \) -delete;
rm -Rf $(find . -type d -name '.AppleDouble');
find . -type d -not \( -name storage -prune \) -exec chmod 0775 {} \; ;

# make executable any file that contains a shebang header
chmod 0775 $(grep -rEl -m 1 --exclude-dir={vendor,node_modules,.git} '#!/' .) vendor/bin/*;
mkdir -p wp-content/uploads wp-content/plugins

chgrp -Rf www-dev .
chmod -Rf ug+rw,o-w .

echo "Done";
