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


# USAGE : positionner numero de course (RACE=) 
#    puis ...  valideNORD longitude-ouest longitude-est latitude NumeroWP

RACE=20081109
valideNORD    1000      11000      -42000  1
valideNORD   40500      50500      -48333  3
valideNORD  103000     113000      -47000  5
valideNORD  136000     147000      -52000  7
valideNORD -180000    -170000      -50500  8
valideNORD -151000    -141000      -50500  9
valideNORD -121000    -110000      -52000 10
