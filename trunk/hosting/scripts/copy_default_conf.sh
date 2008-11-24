#!/bin/bash
# source d'inspiration : v2.openesub.org / Auteur : paparazzia@gmail.com / Licence : GPL2

#Copie des fichiers de conf de database et de site dans ~/conf
#un truc de feignant...

#Commun aux scripts
source $VLMRACINE/conf/conf_script || exit 1

#cp $VLMTEMP/base/config/config.sh.dist $VLMCONF/conf_database.dist
#cp $VLMTEMP/site/application/settings/config.ini.dist  $VLMCONF/conf_site.dist
#cp $VLMTEMP/engine/rorqual/openConstantes.py $VLMCONF/conf_engine.dist
#cp $VLMTEMP/hosting/conf/*.dist $VLMCONF/

echo "Copie OK !"
echo "N'oubliez pas de renommer puis modifier les fichiers en .dist du répertoire de conf" 
