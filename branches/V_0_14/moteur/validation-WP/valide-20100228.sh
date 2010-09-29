#!/bin/bash
mysql -e "select distinct P.idusers from positions P ,users U where (\`long\` between -25000 and -0001) and lat >= -50000 and P.idusers>0 and P.idusers=U.idusers and U.nextwaypoint=2 and  P.race=U.engaged and U.engaged=20100228" | tail -n +2 | while read idusers ; do 
	udp=$(mysql -e "select max(userdeptime) from waypoint_crossing where idusers=$idusers and idraces=20100228;"  | tail -n +2)
	mysql -e "insert into  waypoint_crossing values (20100228, 2, $idusers, $(date +%s), $udp) ;"
	mysql -e "update users set nextwaypoint=3 where idusers=$idusers;"
done
