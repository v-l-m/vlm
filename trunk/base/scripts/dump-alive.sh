#!/bin/bash

source $VLMRACINE/conf/conf_base

dumpname="vlmdump-alive.sql"

#dont dump : histpos modules_status
TBNAMES="admin_changelog auto_pilot flags players players_pending playerstousers positions races races_instructions races_loch races_ranking races_results races_waypoints racesmap updates user_action user_prefs users waypoint_crossing waypoints"

echo "Dumping living tables sql to $dumpname"

mysqldump -h $DBSERVER -u $DBUSER --password=$DBPASSWORD --add-locks --add-drop-table --insert-ignore --no-create-db --disable-keys $DBNAME $TBNAMES > $dumpname

echo "Zipping file to $dumpname.gz"

gzip $dumpname

echo "OK"
