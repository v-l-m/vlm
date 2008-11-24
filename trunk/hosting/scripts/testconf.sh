#!/bin/bash
# source d'inspiration : v2.openesub.org / Auteur : paparazzia@gmail.com / Licence : GPL2

#pour tester la configuration
if test "$VLMRACINE" = ""; then
    echo "ERREUR : Veuillez définir la variable d'environnement VLMRACINE"
    echo "HINT : Par exemple dans votre .bashrc"
    exit 1
fi

source $VLMRACINE/conf/conf_script || { echo "ERREUR: votre fichier de configuration n'est pas disponible"; exit 1;}

#FIXME test des fichiers _conf
if test ! -e $VLMCONF/conf_database ; then
    echo "ERREUR: le fichier $VLMCONF/conf_database n'existe pas !"
    exit 1
fi
if test ! -e $VLMCONF/conf_site ; then
    echo "ERREUR: le fichier $VLMCONF/conf_site n'existe pas !"
    exit 1
fi

echo "Configuration des scripts OK !"
