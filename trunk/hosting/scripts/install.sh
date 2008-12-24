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
echo "-les répertoires conf et scripts de VLM dans le répertoire courant: $pwd"
echo "-la version $VLMSVNBASE de ces répertoires"
echo "Vérifiez également que la commande `svn` est disponible."
read -n1 -r -p "Tapez Ctrl-C pour stopper la procédure maintenant ou n'importe quelle touche pour continuer" key

#Nettoyage
echo " "
echo " "
echo "+Suppression des répertoires pré-existants $pwd/conf et $pwd/scripts"
rm -Rf conf
rm -Rf scripts

#installation de la conf (non paramètrée) et des scripts
echo "+Installation de $pwd/conf & $pwd/scripts"
svn export http://dev.virtual-loup-de-mer.org/svn/vlm/$VLMSVNBASE/hosting/conf conf  --username anonymous --password ""
svn export http://dev.virtual-loup-de-mer.org/svn/vlm/$VLMSVNBASE/hosting/scripts scripts  --username anonymous --password ""

#on fixe (temporairement) le VLMRACINE
export VLMRACINE=`pwd`
echo "+La variable VLMRACINE a été fixée temporairement à la valeur $VLMRACINE pour la durée de la session courante"

echo " "
echo "La suite : "
echo "* fixer de façon permanente (dans /etc/profile ou dans le bashrc) la variable VLMRACINE au path courant"
echo "* editer les fichiers ./conf/*.dist et supprimer l'extension .dist"
echo "* lancer le scripts ./scripts/testconf.sh"
