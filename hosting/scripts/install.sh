#!/bin/bash
# Pseudo installateur de vlm

pwd=`pwd`

VLMSVNBASE=$1
if test "$VLMSVNBASE" = ""; then
  echo "Ce script s'appelle avec 'source install.sh [trunk|nom_de_la_branche]"
  exit 0
fi
if test "$VLMSVNBASE" != "trunk"; then
  VLMSVNBASE="branches/$VLMSVNBASE"
fi

echo "Vous allez installer:"
echo "- les répertoires conf et scripts de VLM dans le répertoire courant: $pwd"
echo "- la version $VLMSVNBASE de ces répertoires"
echo "Vérifiez également les prérequis :"
echo "- que la commande 'svn' est disponible. (paquet debian : subversion)"
echo "- que php est installé (paquets debian : php5 php5-cli php-config php5-dev)" 
read -n1 -r -p "Tapez Ctrl-C pour stopper la procédure maintenant ou n'importe quelle touche pour continuer" key

#Nettoyage
echo " "
echo " "
echo "+Suppression des répertoires pré-existants $pwd/conf et $pwd/scripts"
rm -Rf conf
rm -Rf scripts

#installation de la conf (non paramètrée) et des scripts
echo "+Installation de $pwd/conf & $pwd/scripts"
svn export https://github.com/v-l-m/vlm/$VLMSVNBASE/hosting/conf conf
svn export https://github.com/v-l-m/vlm/$VLMSVNBASE/hosting/scripts scripts

#on fixe (temporairement) le VLMRACINE
export VLMRACINE=`pwd`
echo "+La variable VLMRACINE a été fixée temporairement à la valeur $VLMRACINE pour la durée de la session courante"
echo "export VLMRACINE=`pwd`" >> .bashrc
echo "export VLMRACINE=`pwd`" >> .profile
echo "+La variable VLMRACINE a été fixée à la valeur $VLMRACINE dans .bashrc et .profile pour les sessions suivantes"
echo " "
echo "La suite : "
echo "* editer les fichiers ./conf/*.dist et supprimer l'extension .dist"
echo "* lancer le scripts ./scripts/testconf.sh"
