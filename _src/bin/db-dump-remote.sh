#!/bin/bash

basePath=$(realpath "`dirname "$0"`/..");

cd "`dirname "$0"`/..";

envfile=$(realpath '../config/.env.prod');
if [ ! -f "$envfile" ]; then
    echo "Missing production dotenv file: $envfile"
    exit 1;
fi

. "$envfile";

sshuser='kfrymire';
sshhost='sec.agent.arraybcconnect.com';
dbhost=$DB_HOST;
dbuser=$DB_USER;
dbname=$DB_NAME;
dbpass=$DB_PASSWORD;
dbpath='db-content/latest';
dbfile="$dbpath/dump.sql.gz";

if [ -f "$dbfile" ]; then
    dbprev='db-content/previous';
    mkdir -p "$dbprev"
    mv "$dbfile" "$dbprev/dump-$(date -r $dbfile '+%F').sql.gz";
fi;

mkdir -p "$dbpath";
echo -ne "\nDumping remote database $dbname...\n";
echo "Connecting to ${sshuser}@${sshhost}";

dumpcmd="mysqldump -h $dbhost -u '$dbuser' -p'$dbpass' --no-tablespaces --quick --complete-insert --set-gtid-purged=OFF '$dbname' | gzip > ${dbname}.sql.gz";
ssh -o 'StrictHostKeyChecking=no' "${sshuser}@${sshhost}" "$dumpcmd"
scp "${sshuser}@${sshhost}:${dbname}.sql.gz" "$dbfile";
ssh -o 'StrictHostKeyChecking=no' "${sshuser}@${sshhost}" "rm ${dbname}.sql.gz"

echo "Saved: $dbfile";
