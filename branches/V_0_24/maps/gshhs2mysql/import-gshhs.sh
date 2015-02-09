#!/bin/bash
#pour récupérer le VLMJEUROOT
source $VLMRACINE/conf/conf_script
source $VLMRACINE/conf/conf_base

PATH_TO_GSHHS=$VLMGSHHS

for i in 'c' 'l' 'i' 'h' 'f'; do
	echo "Working on $i"
	./gshhs2csv $PATH_TO_GSHHS/gshhs_$i.b > $VLMTEMP/coastline_$i.csv
	mysqlimport --lock-tables --delete --local --host="$DBSERVER" --fields-terminated-by="," --lines-terminated-by="\n" --columns=idpoint,idcoast,latitude,longitude --user="$DBUSER" --password="$DBPASSWORD" "$DBNAME" $VLMTEMP/coastline_$i.csv
	rm -f $VLMTEMP/coastline_$i.csv
done


