#!/bin/bash

source $VLMRACINE/conf/conf_base || exit 1

MYSQL="mysql -u $DBUSER -h $DBSERVER -p$DBPASSWORD $DBNAME"

function interditSUD()
{
	WEST=$1
        EAST=$2
	LATITUDE=$3
        let NEXTWP=WP+1

        NOW=$(date +%s -u)
        let NOW+=300

        ## Non optimal : Remise de la bonne polaire à tous les bateaux pour traiter le cas
         #               des bateaux ressortis d'une nosailzone par l'est ou l'ouest.
 	$MYSQL -e "update users set boattype='boat_$BOAT' where engaged=$RACE;"

        # On met en mode IceBreaker les pixels dans la nosailzone
	$MYSQL -e "select distinct P.idusers from positions P ,users U 
                         where (\`long\` between $WEST and $EAST) 
                         and lat <= $LATITUDE 
                         and P.idusers>0 and P.idusers=U.idusers 
                         and P.time > $NOW - 600
                         and P.race=U.engaged and U.engaged=$RACE" | tail -n +2 | while read idusers ; do 

		$MYSQL -e "update users set boattype='boat_IceBreaker', blocnote='IceBreaker mode (60 South Limit)' where idusers=$idusers;"
	done 

##        # A prevoir : Remettre automatiquement une polaire de $BOAT 
##        # à ceux ne s'étant pas trouvés dans la zone interdite depuis plus de X minutes
##        $MYSQL -e "select distinct P.idusers from positions P ,users U 
##                          where (\`long\` between $WEST and $EAST) 
##                          and lat >= $LATITUDE 
##                          and P.idusers>0 and P.idusers=U.idusers 
##                          and P.time > $NOW - 600
##                          and P.race=U.engaged and U.engaged=$RACE" | tail -n +2 | while read idusers ; do 
## 
## 		$MYSQL -e "update users set boattype='boat_$BOAT' where idusers=$idusers;"
## 	done 

}



# USAGE : positionner numero de course (RACE=) 
#    et   ...  interditSUD longitude-ouest longitude-est latitude 

# Tour du monde à l'envers
BOAT=C5bp5
RACE=201085
interditSUD   -180000     180000  -60000

# TJV
BOAT=C5bp5
RACE=81
interditSUD   -180000     180000  -60000  

# BWR
BOAT=Imoca2008
RACE=20101231
interditSUD   105000     120000  -46000  

