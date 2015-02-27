#!/bin/bash

source $VLMRACINE/conf/conf_base

dumpname=$1

if test "$dumpname" = ""; then
    dumpname="vlmdump-history.sql"
fi

TBNAMES="histpos"

echo "Dumping big archive sql tables (gzipped) to $dumpname.gz"

mysqldump -h $DBSERVER -u $DBUSER --password=$DBPASSWORD --add-locks --add-drop-table --insert-ignore --no-create-db --disable-keys $DBNAME $TBNAMES | gzip -9 -c > $dumpname.gz

echo "OK"
