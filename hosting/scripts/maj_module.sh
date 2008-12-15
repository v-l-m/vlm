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

#Recopie de la conf si nécessaire // Postdéploiement
echo " "
echo -n "Post-déploiement, mise en place de droits et de la configuration... pour $confmodule ..."

case $confmodule in
#    base)
#    Pas de post déploiement pour l'instant
#    ;;
    site)
    #cp $VLMJEUROOT/lib/phpcommon/* $destmodulepath/ || exit 1
    echo "$VLMSVNBRANCH" >> $destmodulepath/version.txt
    echo "<br />" >> $destmodulepath/version.txt
    date +"%m/%d/%y %X %Z" >> $destmodulepath/version.txt
    rm $destmodulepath/images
    ln -s $destmodulepath/../medias/images images
    ;;
    moteur)
    cp $VLMRACINE/conf/conf_script $destmodulepath/
    #cp $VLMJEUROOT/lib/phpcommon/* $destmodulepath/ || exit 1
    ;;
    lib/phpcommon)
    confsrc=param.php
    confpath=param.php
    echo "Copie de $VLMCONF/$confsrc vers $destmodulepath/$confpath"
    cp -f $VLMCONF/$confsrc $destmodulepath/$confpath || exit 1
    echo "La recopie de phpcommon dans site & moteur n'est plus nécessaire car on fixe l'include path dans la conf du vhost"
    #cp $VLMJEUROOT/lib/phpcommon/* $VLMJEUROOT/site/ || exit 1
    #cp $VLMJEUROOT/lib/phpcommon/* $VLMJEUROOT/moteur/ || exit 1
    ;;
    lib/vlm-c)
    oldpwd=$PWD
    cd $destmodulepath
    make php || exit 1
    cp php/vlmc.so $VLMVLMCSO/
    cp php/vlmc.php $VLMVLMCPHP/vlmc.php
    cd $oldpwd
    echo "/// ATTENTION /// VOUS DEVEZ REDEMARRER APACHE... ETES VOUS ROOT OU SUDOER ?"
    ;;
    *)
    
    echo "Pas de post déploiement / fichiers de conf à mettre à jour"
    exit 0
    ;;
esac

echo "Ok!"
