#!/bin/bash

source $VLMRACINE/conf/conf_base || exit 1

MYSQL="mysql -u $DBUSER -h $DBSERVER -p$DBPASSWORD $DBNAME"

function launchTrophyControl {
  # NOW=$(date +%s -u)
  # let NOW+=300

 	$MYSQL -e "call $1(NULL);"
}



# USAGE : lancement des v�rifs de troph�es  
# launchTrophyControl sptrophycontrolperdays
launchTrophyControl sptrophycontrolconnexion

