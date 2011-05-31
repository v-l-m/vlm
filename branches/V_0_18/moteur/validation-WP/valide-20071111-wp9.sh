#!/bin/bash
mysql -e "select distinct P.idusers from positions P ,users U where (\`long\` between -126000 and -115000) and lat >= -55000 and P.idusers>0 and P.idusers=U.idusers and U.nextwaypoint=9" | tail -n +2 | while read idusers ; do 
	mysql -e "update users set nextwaypoint=10 where idusers=$idusers;"
	udp=$(mysql -e "select max(userdeptime) from waypoint_crossing where idusers=$idusers and idraces=20071111;"  | tail -n +2)
	mysql -e "insert into  waypoint_crossing values (20071111, 9, $idusers, $(date +%s), $udp) ;"
done
