#!/bin/bash
mysql -e "select distinct P.idusers from positions P ,users U where (\`long\` between 20000 and 67000) and P.idusers>0 and P.idusers=U.idusers and U.nextwaypoint=5" | tail -n +2 | while read idusers ; do 
	mysql -e "update users set nextwaypoint=6 where idusers=$idusers;"
done
