#!/bin/bash

source $VLMRACINE/conf/conf_base

dumpname="vlmdump.sql"

echo "Dumping sql to ${dumpname}.gz"

mysqldump -h $DBSERVER -u $DBUSER --password=$DBPASSWORD --add-locks --add-drop-table --insert-ignore --no-create-db --disable-keys $DBNAME | gzip -9 -c > ${dumpname}.gz

echo "OK"
