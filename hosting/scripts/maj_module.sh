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

source $VLMRACINE/conf/conf_base
if test $? -eq 0 ; then
    REVID=`svn info $VLMSVNFULL --username anonymous --password="vlm" | sed -n '/vision/ s/[^0-9]//g p'|head -n 1`
    echo "INSERT INTO modules_status (serverid, moduleid, revid) VALUES ('`hostname -i`', '$confmodule', $REVID);"|mysql -h $DBSERVER -u $DBUSER --password=$DBPASSWORD $DBNAME
    echo "Log du deploiement : OK !"
fi

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
    cd $destmodulepath/gshhs2mysql
    make
    ./import-gshhs.sh
    ;;
    clip_gshhs)
    echo "+$confmodule : Fabrication de tiles_g"
    oldpwd=$PWD
    cd $destmodulepath
    make tiles_g || exit 1
    echo -n "+$confmodule: installation de tiles_g dans $VLMBIN..."
    cp tiles_g $VLMBIN/tiles_g
    echo 'OK !'
    if test -e "$VLMGSHHS/rivers-f-1.dat" ; then
        echo "++$confmodule: les fichiers de bdd gshhs existent déjà !"
        exit 0
    else 
        echo "+$confmodule: Download des fichiers poly, rivers et borders"
        wget --output-document="$VLMTEMP/poly-f-1.dat" "http://dev.virtual-loup-de-mer.org/poly-f-1.dat"
        echo -n "+$confmodule: copie dans  $VLMGSHHS..."
        mv "$VLMTEMP/poly-f-1.dat" "$VLMGSHHS/"
        echo 'OK !'
        wget --output-document="$VLMTEMP/rivers-f-1.dat" "http://dev.virtual-loup-de-mer.org/rivers-f-1.dat"
        echo -n "+$confmodule: copie dans  $VLMGSHHS..."
        mv "$VLMTEMP/rivers-f-1.dat" "$VLMGSHHS/"
        echo 'OK !'
        wget --output-document="$VLMTEMP/borders-f-1.dat" "http://dev.virtual-loup-de-mer.org/borders-f-1.dat"
        echo -n "+$confmodule: copie dans  $VLMGSHHS..."
        mv "$VLMTEMP/borders-f-1.dat" "$VLMGSHHS/"
        echo 'OK !'
        wget --output-document="$VLMTEMP/ETOPO1_Ice.dat" "http://dev.virtual-loup-de-mer.org/ETOPO1_Ice.dat"
        echo -n "+$confmodule: copie dans  $VLMGSHHS..."
        mv "$VLMTEMP/ETOPO1_Ice.dat" "$VLMGSHHS/"
        echo 'OK !'
    fi
    cd $oldpwd
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
    echo -n "+$confmodule: Mise en place du lien symbolique vers le mode spectateur..."
    ln -s $destmodulepath/../guest_map $destmodulepath/guest_map
    echo 'OK !'
    echo -n "+$confmodule: Mise en place du lien symbolique vers jvlm..."
    ln -s $destmodulepath/../jvlm $destmodulepath/jvlm
    echo 'OK !'
    echo -n "+$confmodule: Mise en place du lien symbolique vers externals..."
    ln -s $destmodulepath/../externals $destmodulepath/externals
    echo 'OK !'

    echo -n "+$confmodule: Création du cache si nécessaire"
    mkdir -p $VLMCACHE/racemaps --mode=777
    mkdir -p $VLMCACHE/minimaps --mode=777
    mkdir -p $VLMCACHE/flags --mode=777
    mkdir -p $VLMCACHE/gshhstiles --mode=777
    mv $destmodulepath/cache.htaccess $VLMCACHE/.htaccess
    echo 'OK !'    
    echo -n "+$confmodule: Mise en place du lien symbolique vers le cache..."
    ln -s $VLMCACHE $destmodulepath/cache
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
    echo -n "+$confmodule: installation du script init.d de démarrage du (wind|polar)server dans $VLMBIN..."
    cp $destmodulepath/windserver.sh $VLMBIN/windserver.sh
    echo 'OK !'
    ;;
#    medias)
#    echo -n "+$confmodule: Nothing to do"
#    ;;
    *)
    #Tous les autres modules
    echo "+$confmodule: Pas de post déploiement"
    exit 0
    ;;
esac

echo "Mise à jour du module $confmodule OK!"
