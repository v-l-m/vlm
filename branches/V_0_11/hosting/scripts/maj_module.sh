#!/bin/bash
# source d'inspiration : v2.openesub.org / Auteur : paparazzia@gmail.com / Licence : GPL2

#- met à jour un module depuis le svn
#- copie le svn dans VLMJEUROOT
#- met le(s) fichiers de conf à jour
#- effectue les post-opérations (compilations, lien symboliques, etc...)

#Commun aux scripts
source $VLMRACINE/conf/conf_script || exit 1

#Le premier argument est le nom du module
confmodule=$1

#Récupération svn
$VLMSCRIPTS/svn_maj_module.sh $confmodule

if test $? -ne 0 ; then
    echo "!!! Erreur lors de la récupération depuis le subversion"
    exit 1
fi

curmodulepath=$VLMTEMP/$confmodule
destmodulepath=$VLMJEUROOT/$confmodule

#copie dans VLMJEUROOT
echo " "
echo " "
echo "Etape 2 : Nettoyage de $destmodulepath et remplacement par la version de $VLMTEMP"
echo ' '
echo -n "+Nettoyage de $destmodulepath..."
rm -Rf $destmodulepath
if test -d $destmodulepath ; then
  echo "!!! Le repertoire $destmodulepath existe !!!"
  exit 1
fi
mkdir -p $destmodulepath
echo "OK !"
echo -n "+Remplacement par l'extraction du svn..."
cp -Rf $curmodulepath/* $destmodulepath
echo 'OK !'

#Recopie de la conf si nécessaire // Postdéploiement
echo " "
echo "+Post-déploiement, mise en place de droits et de la configuration... pour $confmodule ..."

case $confmodule in
    #base)
    #Pas de post déploiement pour l'instant
    #;;
    maps)
    echo "+$confmodule : Test & déploiement des fichiers gshhs"
    $destmodulepath/init-gshhs.sh
    ;;
    site)
    echo -n "+$confmodule: Constitution et copie du fichier de version..."
    echo "$VLMSVNBRANCH" >> $destmodulepath/version.txt
    echo "<br />" >> $destmodulepath/version.txt
    date +"%m/%d/%y %X %Z" >> $destmodulepath/version.txt
    echo 'OK !'
    echo -n "+$confmodule: Mise en place du .htaccess..."
    cp $VLMRACINE/conf/conf_htaccess_site $destmodulepath/.htaccess
    echo 'OK !'    
    echo -n "+$confmodule: Mise en place du lien symbolique vers les images du module medias..."
    ln -s $destmodulepath/../medias/images $destmodulepath/images
    echo 'OK !'
    echo -n "+$confmodule: Mise a jour des permissions de media/images/minimaps & racemaps & pavillons"
    chmod -R 777 $destmodulepath/../medias/images/minimaps
    chmod -R 777 $destmodulepath/../medias/images/racemaps
    chmod -R 777 $destmodulepath/../medias/images/pavillons
    echo 'OK !'

    echo -n "+$confmodule: Constitution de la liste des polaires..."
    
    mkdir -p $VLMPOLARS
    cp $destmodulepath/Polaires/* $VLMPOLARS/
    for i in `ls $VLMPOLARS` ; do
        if [ $i != polars.list ] ; then
            echo -n "$i"|sed 's/boat_\(.*\)\.csv$/\1/' >> $VLMPOLARS/polars.list.tmp
            echo ":$VLMPOLARS/$i" >> $VLMPOLARS/polars.list.tmp
            fi;
        done ;
    mv $VLMPOLARS/polars.list.tmp $VLMPOLARS/polars.list
    echo 'OK !'

    echo -n "+$confmodule: Mise à jour polarserver"
    $VLMBIN/polarserver $VLMPOLARS/polars.list
    ;;

    moteur)
    echo -n "+$confmodule: Copie de la configuration des scripts..."
    cp $VLMRACINE/conf/conf_script $destmodulepath/
    echo 'OK !'
    ;;

    lib/phpcommon)
    confsrc=param.php
    confpath=param.php
    echo -n "+$confmodule: Copie de $VLMCONF/$confsrc vers $destmodulepath/$confpath"
    cp -f $VLMCONF/$confsrc $destmodulepath/$confpath || exit 1
    echo 'OK !'
    ;;

    lib/vlm-c)
    echo "+$confmodule: Compilation de vlm-c"
    oldpwd=$PWD
    cd $destmodulepath
    make all php || exit 1
    echo -n "+$confmodule: installation de la librairie dynamique dans $VLMVLMCSO..."
    cp php/vlmc.so $VLMVLMCSO/
    echo 'OK !'
    echo -n "+$confmodule: installation du wrapper dans $VLMVLMCPHP..."
    cp php/vlmc.php $VLMVLMCPHP/vlmc.php
    echo 'OK !'
    echo -n "+$confmodule: installation du windserver dans $VLMBIN..."
    cp windserver $VLMBIN/windserver
    chmod a+rx $VLMBIN/windserver
    echo 'OK !'
    echo -n "+$confmodule: installation du polarserver dans $VLMBIN..."
    cp polarserver $VLMBIN/polarserver
    chmod a+rx $VLMBIN/polarserver
    echo 'OK !'

    cd $oldpwd
    echo "!!! ATTENTION /// VOUS DEVEZ REDEMARRER APACHE... ETES VOUS ROOT OU SUDOER ?"
    ;;
    grib)
    echo -n "+$confmodule: installation du script de récupération des gribs dans $VLMBIN..."
    cp $destmodulepath/noaa.sh $VLMBIN/noaa.sh
    cp $destmodulepath/noaa-slave.sh $VLMBIN/noaa-slave.sh
    echo 'OK !'
    ;;
    medias)
    echo -n "+$confmodule: Mise a jour des permissions de media/images/minimaps & racemaps & pavillons"
    chmod -R 777 $destmodulepath/images/minimaps
    chmod -R 777 $destmodulepath/images/racemaps
    chmod -R 777 $destmodulepath/images/pavillons
    ;;
    *)
    #Tous les autres modules
    echo "+$confmodule: Pas de post déploiement"
    exit 0
    ;;
esac

echo "Mise à jour du module $confmodule OK!"
