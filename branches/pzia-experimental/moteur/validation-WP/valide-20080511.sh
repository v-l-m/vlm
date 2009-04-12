#!/bin/bash
mysql -e "select distinct P.idusers from positions P ,users U where (\`long\` between -50000 and -47000) and lat <= 40000 and P.idusers>0 and P.idusers=U.idusers and U.nextwaypoint=1 and  P.race=U.engaged and U.engaged=2008051140" | tail -n +2 | while read idusers ; do 
	udp=$(mysql -e "select max(userdeptime) from waypoint_crossing where idusers=$idusers and idraces=2008051140;"  | tail -n +2)
	mysql -e "insert into  waypoint_crossing values (2008051140, 1, $idusers, $(date +%s), $udp) ;"
	mysql -e "update users set nextwaypoint=2 where idusers=$idusers;"
done

mysql -e "select distinct P.idusers from positions P ,users U where (\`long\` between -50000 and -47000) and lat <= 40000 and P.idusers>0 and P.idusers=U.idusers and U.nextwaypoint=1 and P.race=U.engaged and U.engaged=2008051160" | tail -n +2 | while read idusers ; do 
	udp=$(mysql -e "select max(userdeptime) from waypoint_crossing where idusers=$idusers and idraces=2008051160;"  | tail -n +2)
	mysql -e "insert into  waypoint_crossing values (2008051160, 1, $idusers, $(date +%s), $udp) ;"
	mysql -e "update users set nextwaypoint=2 where idusers=$idusers;"
done
