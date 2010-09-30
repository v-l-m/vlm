#!/bin/bash
function interditSUD()
{
	WEST=$1
        EAST=$2
	LATITUDE=$3
        let NEXTWP=WP+1

        NOW=$(date +%s -u)
        let NOW+=300

        # On met en mode IceBreaker les pixels au sud de 60 sud
	mysql -e "select distinct P.idusers from positions P ,users U 
                         where (\`long\` between $WEST and $EAST) 
                         and lat <= $LATITUDE 
                         and P.idusers>0 and P.idusers=U.idusers 
                         and P.time > $NOW - 600
                         and P.race=U.engaged and U.engaged=$RACE" | tail -n +2 | while read idusers ; do 

		mysql -e "update users set boattype='boat_IceBreaker', blocnote='IceBreaker mode (60 South Limit)' where idusers=$idusers;"
	done 

        # A prevoir : Remettre automatiquement une polaire de $BOAT 
        # à ceux  ne s'étant pas trouvés dans la zone interdite depuis plus de 30 minutes
        mysql -e "select distinct P.idusers from positions P ,users U 
                          where (\`long\` between $WEST and $EAST) 
                          and lat >= $LATITUDE 
                          and P.idusers>0 and P.idusers=U.idusers 
                          and P.time > $NOW - 600
                          and P.race=U.engaged and U.engaged=$RACE" | tail -n +2 | while read idusers ; do 
 
 		mysql -e "update users set boattype='boat_$BOAT' where idusers=$idusers;"
 	done 



}



# USAGE : positionner numero de course (RACE=) 
#    et   ...  interditSUD longitude-ouest longitude-est latitude 

BOAT=C5bp5
RACE=201085
interditSUD   -180000     180000  -60000

BOAT=C5bp5
RACE=81
interditSUD   -180000     180000  -60000  

