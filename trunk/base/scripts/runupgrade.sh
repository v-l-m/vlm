#!/bin/bash

if test "$1" = ""; then
  echo "Ce script s'appelle avec le nom de la version de destination"
  echo " Exemple : ./runupgrade.sh 0.11"
  exit 0
fi

echo -n "Looking for $VLMRACINE/conf/conf_script"
source $VLMRACINE/conf/conf_script || exit 1
echo " : OK"

echo "Running v$1 upgrade scripts"

sqlfile=upgrade-v$1.sql
phpfile=upgrade-v$1.php
if [ -f $sqlfile ]; then
    echo -n "Looking for $VLMRACINE/conf/conf_base"
    source $VLMRACINE/conf/conf_base || exit 1
    echo " : OK"
    echo "Running sql script $sqlfile"
    mysql -v -h $DBSERVER -u $DBUSER --password=$DBPASSWORD $DBNAME < $sqlfile
fi

if [ -f $phpfile ]; then
    echo "Running php script $phpfile"
    $VLMPHPPATH $phpfile
fi


