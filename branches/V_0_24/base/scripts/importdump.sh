#!/bin/bash

dumpfile=$1

if [ \( ! -f "$dumpfile" \) -o \( "$dumpfile" = "" \) ]  ; then
    echo "!!! ERREUR : Veuillez fournir un fichier dump gzippé en argument"
    exit 1;
fi


source $VLMRACINE/conf/conf_base

gunzip -c $dumpfile|mysql -h $DBSERVER -u $DBUSER --password=$DBPASSWORD $DBNAME
