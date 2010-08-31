#!/bin/sh
#pour récupérer le VLMJEUROOT
source $VLMRACINE/conf/conf_script

PATH_TO_GSHHS=$VLMGSHHS

DB_USERNAME=$DBUSER
DB_DBNAME=$DBPASSWORD

for i in 'c' 'l' 'i' 'h' 'f'; do
	echo "Working on $i"
	./gshhs2csv $PATH_TO_GSHHS/gshhs_$i.b > coastline_$i.csv
	mysqlimport --fields-terminated-by="," --lines-terminated-by="\n" --columns=idpoint,idcoast,latitude,longitude --user="$DB_USERNAME" --password="$DB_DBNAME" coastline_i$.csv
	rm -f coastline_$i.csv
done


