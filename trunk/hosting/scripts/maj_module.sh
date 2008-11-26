#!/bin/bash
# source d'inspiration : v2.openesub.org / Auteur : paparazzia@gmail.com / Licence : GPL2

#- met à jour un module depuis le svn
#- copie le svn dans jeu
#- met le fichiers de conf

#Commun aux scripts
source $VLMRACINE/conf/conf_script || exit 1

confmodule=$1

#Récupération svn
$VLMSCRIPTS/svn_maj_module.sh $confmodule

if test $? -ne 0 ; then
    exit 1
fi

curmodulepath=$VLMTEMP/$confmodule
destmodulepath=$VLMJEUROOT/$confmodule

#copie dans jeu
echo " "
echo " "
echo "Etape 2 : Nettoyage de $destmodulepath et remplacement par la version de $VLMTEMP"
echo ' '
echo -n "Nettoyage de $destmodulepath"
rm -Rf $destmodulepath
if test -d $destmodulepath ; then
  echo "Le repertoire $destmodulepath existe !!!"
  exit 1
fi
mkdir -p $destmodulepath
echo "Ok !"
echo -n "Remplacement..."
cp -Rf $curmodulepath/* $destmodulepath
echo 'Ok !'

#Recopie de la conf si nécessaire
echo " "
echo "Post-déploiement"

#NB : présuppose un seul fichier de conf par module
case $confmodule in
#    base)
#    Pas de fichier de conf base pour l'instant
#    ;;
    site)
    confsrc=conf_php
    confpath=param.php
    ;;
    moteur)
    confsrc=conf_php
    confpath=param.php
    ;;
#    phpcommon)
#    ;;
    *)
    
    echo "Pas de fichier de conf à mettre à jour."
    exit 0
    ;;
esac

echo "Copie de $VLMCONF/$confsrc vers $destmodulepath/$confpath"
echo -n "Mise en place de droits et de la configuration... pour $confmodule ..."
cp -f $VLMCONF/$confsrc $destmodulepath/$confpath || exit 1
echo "Ok!"
