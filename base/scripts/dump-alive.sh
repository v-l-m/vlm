#!/bin/bash

source $VLMRACINE/conf/conf_base

dumpname=$1

if test "$dumpname" = ""; then
    dumpname="vlmdump-alive.sql"
fi

#dont dump : histpos modules_status
#FIXME : les coastline ne sont pas exportés (nulle part...)
TBNAMES="admin_changelog auto_pilot flags players players_pending playerstousers positions races racespreview races_instructions races_loch races_ranking races_results races_waypoints racesmap updates user_action user_prefs users waypoint_crossing waypoints news racestogroups racesgroups modules_status nszracesegment nszsegment"

echo "Dumping live sql tables (gzipped) to $dumpname.gz"

mysqldump -h $DBSERVER -u $DBUSER --password=$DBPASSWORD --add-locks --add-drop-table --insert-ignore --no-create-db --disable-keys $DBNAME $TBNAMES | gzip -9 -c > $dumpname.gz

echo "OK"
