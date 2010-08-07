#!/bin/bash

source $VLMRACINE/conf/conf_base

dumpname="vlmdump.sql"

echo "Dumping sql to $dumpname"

mysqldump -h $DBSERVER -u $DBUSER --password=$DBPASSWORD --add-drop-table $DBNAME > $dumpname

echo "Zipping file to $dumpname.gz"

gzip $dumpname

echo "OK"
