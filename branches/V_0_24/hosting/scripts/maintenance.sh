#!/bin/bash
#- pose le .htaccess de maintenance dans site
#- lock du moteur pendant 120s

#Commun aux scripts
source $VLMRACINE/conf/conf_script || exit 1

#Le premier argument est le message complémentaire
echo $1 > $VLMJEUROOT/site/maintenance.txt

cp $VLMRACINE/conf/maintenance.htaccess $VLMJEUROOT/site/.htaccess

echo "Website is in maintenance"

while [ -f $VLMTEMP/cronvlm..lock ] || [ -f $VLMTEMP/cronvlm-clean..lock ] ; do
    echo "Waiting unlock from engine"
    sleep 1;
done

echo "Locking engine for 120s"
touch $VLMTEMP/cronvlm-clean..lock
touch $VLMTEMP/cronvlm..lock

