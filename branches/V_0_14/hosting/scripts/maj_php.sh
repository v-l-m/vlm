#!/bin/bash
# source d'inspiration : v2.openesub.org / Auteur : paparazzia@gmail.com / Licence : GPL2

#Commun aux scripts
source $VLMRACINE/conf/conf_script || exit 1

#Récupération svn
$VLMSCRIPTS/maj_module.sh lib/phpcommon
$VLMSCRIPTS/maj_module.sh site
$VLMSCRIPTS/maj_module.sh moteur

