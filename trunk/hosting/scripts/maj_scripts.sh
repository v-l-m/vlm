#!/bin/bash
# source d'inspiration : v2.openesub.org / Auteur : paparazzia@gmail.com / Licence : GPL2

#met à jour les scripts

#Commun aux scripts
source $VLMRACINE/conf/conf_script || exit 1

#Récupération svn
$VLMSCRIPTS/svn_maj_module.sh hosting

if test $? -ne 0 ; then
    exit 1
fi

cp $VLMTEMP/hosting/scripts/*.sh $VLMSCRIPTS/
