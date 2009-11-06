#!/bin/bash
function valideNORD()
{
	WEST=$1
        EAST=$2
	LATITUDE=$3
	WP=$4
        let NEXTWP=WP+1
#	echo "select distinct P.idusers from positions P ,users U 
#                         where (\`long\` between $WEST and $EAST) 
#                         and lat >= $LATITUDE 
#                         and P.idusers>0 and P.idusers=U.idusers 
#                         and U.nextwaypoint=$WP
#                         and P.race=U.engaged and U.engaged=$RACE" 
#        return 0;

	mysql -e "select distinct P.idusers from positions P ,users U 
                         where (\`long\` between $WEST and $EAST) 
                         and lat >= $LATITUDE 
                         and P.idusers>0 and P.idusers=U.idusers 
                         and U.nextwaypoint=$WP
                         and P.race=U.engaged and U.engaged=$RACE" | tail -n +2 | while read idusers ; do 

		udp=$(mysql -e "select max(userdeptime) from waypoint_crossing where idusers=$idusers and idraces=$RACE;"  | tail -n +2)
		mysql -e "insert into  waypoint_crossing values ($RACE, $WP, $idusers, $(date +%s), $udp) ;"
		mysql -e "update users set nextwaypoint=$NEXTWP where idusers=$idusers;"

	done
}

function interditSUD()
{
	WEST=$1
        EAST=$2
	LATITUDE=$3
        let NEXTWP=WP+1

        NOW=$(date +%s -u)
        let NOW+=240

	mysql -e "select distinct P.idusers from positions P ,users U 
                         where (\`long\` between $WEST and $EAST) 
                         and lat <= $LATITUDE 
                         and P.idusers>0 and P.idusers=U.idusers 
                         and P.time > $NOW - 600
                         and P.race=U.engaged and U.engaged=$RACE" | tail -n +2 | while read idusers ; do 

               
		mysql -e "update users set pilotmode=2,pilotparameter=0,releasetime=$NOW, blocnote='PENALTY : KEEP OUT OF THE FORBIDDEN AREA...' where idusers=$idusers;"

	done
}

# USAGE : positionner numero de course (RACE=) 
#    puis ...  valideNORD longitude-ouest longitude-est latitude NumeroWP
#    et   ...  interditSUD longitude-ouest longitude-est latitude 

RACE=81103
interditSUD  -160000     -100000      -45000  

