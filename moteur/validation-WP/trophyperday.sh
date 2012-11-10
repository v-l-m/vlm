#!/bin/bash

source $VLMRACINE/conf/conf_base || exit 1

MYSQL="mysql -u $DBUSER -h $DBSERVER -p$DBPASSWORD $DBNAME"

function lauchTrophyControl()
{

        NOW=$(date +%s -u)
        let NOW+=300

 	$MYSQL -e "call $1();"

	done 
}



# USAGE : lancement des vérifs de trophées  
launchTrophyControl sptrophycontrolperdays
launchTrophyControl sptrophycontrolconnexion

