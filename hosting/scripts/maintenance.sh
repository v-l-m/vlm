#!/bin/bash
#- pose le .htaccess de maintenance dans site

#Commun aux scripts
source $VLMRACINE/conf/conf_script || exit 1

destmodulepath=$VLMJEUROOT/site

#Le premier argument est le message complémentaire
echo $1 > $destmodulepath/maintenance.txt

cp $VLMRACINE/conf/maintenance.htaccess $destmodulepath/.htaccess

echo "Ce serveur présente désormais la page de maintenance. Redéployer le module site pour rouvrir le service !"
