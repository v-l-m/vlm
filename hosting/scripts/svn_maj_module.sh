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
    site/includes)
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
    clip_gshhs)
    ;;
    guest_map)
    ;;
    jvlm)
    ;;
    externals)
    ;;
    *)
    echo "Précisez : medias, base, moteur, site, grib, maps, lib/vlm-c, lib/phpcommon, clip_gshhs, guest_map, jvlm, externals..."
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
cd $VLMTEMP || exit 1
if test -n "$VLMFAKESVN" ; then
    #Cette option s'active quand la variable VLMFAKESVN est définie (et désigne un repository courant)
    #Elle permet de tester les déploiements en local sans accès au serveur subversion
    #Elle peut aussi servir à tester des déploiements sans avoir besoin de commiter
    echo "Recopie de $current ... depuis $VLMFAKESVN/$svnmodule" 1>&2
    #Création de la destination
    mkdir -p $current
    #Copie brute (à la place de l'export svn)
    cp -Raf $VLMFAKESVN/$svnmodule/* $current
    #nettoyage des répertoires .svn - l'option -depth est là pour qu'on efface d'abords les fils avant les parents.
    find $current  -depth -name "*.svn*" -exec rm -Rf {} \;
else
    echo "Recuperation de $current... depuis $svncurrent" 1>&2
    $VLMSVNPATH export $svncurrent $current
fi
echo "Ok !"
