#!/bin/bash
# source d'inspiration : v2.openesub.org / Auteur : paparazzia@gmail.com / Licence : GPL2

#Commun aux scripts
source $VLMRACINE/conf/conf_script || exit 1

#Récupération svn
$VLMSCRIPTS/maj_module.sh site

if test $? -ne 0 ; then
    exit 1
fi
echo " "
echo -n "Copie du .htaccess dans $VLMDOCUMENTROOT ..."
cp $VLMCONF/site.htaccess $VLMDOCUMENTROOT/.htaccess
echo "Ok!"

