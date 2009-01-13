#!/bin/bash

dumpfile=$1

if [ \( ! -f "$dumpfile" \) -o \( "$dumpfile" = "" \) ]  ; then
    echo "!!! ERREUR : Veuillez fournir un fichier dump en argument"
    exit 1;
fi


source $VLMRACINE/conf/conf_base



mysql -h $DBSERVER -u $DBUSER --password=$DBPASSWORD $DBNAME < $dumpfile
