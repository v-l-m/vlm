#!/bin/bash
# source d'inspiration : v2.openesub.org / Auteur : paparazzia@gmail.com / Licence : GPL2

#Commun aux scripts
source $VLMRACINE/conf/conf_script || exit 1

svnmodule=$1

#Pré déploiement
case $svnmodule in
    medias)
    ;;
    base)
    ;;
    site)
    ;;
    maps)
    ;;
    moteur)
    ;;
    hosting)
    ;;
    grib)
    ;;
    lib/vlm-c)
    ;;
    lib/phpcommon)
    ;;
    *)
    echo "Précisez : medias, base, moteur, site, grib, maps, lib/vlm-c, lib/phpcommon..."
    exit 1
    ;;
esac

clear
echo "************************************"
echo "* MISE A JOUR DE VLM DEPUIS LE SVN *"
echo "************************************"
echo " "
echo "Module  : $svnmodule"
echo "Branche : $VLMSVNBRANCH"
echo "Racine  : $VLMRACINE"
echo " "
echo " "
echo "Etape 1 : Recuperation simple du SVN dans $VLMTEMP"
echo " "

current=$VLMTEMP/$svnmodule
svncurrent=$VLMSVNFULL/$svnmodule
echo -n "Nettoyage de $current... "
rm -Rf $current
if test -d $current; then
  echo "Le repertoire $current existe !!!"
  exit 1
fi
echo "Ok !"
echo "Recuperation de $current... depuis $svncurrent"
cd $VLMTEMP || exit 1
$VLMSVNPATH export $svncurrent $current --username anonymous --password "vlm"
echo "Ok !"
