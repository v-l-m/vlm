#!/bin/bash
#- effectue les post-opérations pour site

#Commun aux scripts
source $VLMRACINE/conf/conf_script || exit 1

destmodulepath=$VLMJEUROOT/site

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
mkdir -p $VLMCACHE/tracks --mode=777
mkdir -p $VLMCACHE/racemaps --mode=777
mkdir -p $VLMCACHE/minimaps --mode=777
mkdir -p $VLMCACHE/tinymaps --mode=777
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

echo -n "+$confmodule: Mise en place du lien symbolique vers les fichiers gshhs..."
ln -s $VLMGSHHS $destmodulepath/gshhs
echo 'OK !'

destmodulepath=$VLMJEUROOT/moteur
confsrc=param.php
confpath=param.php
echo -n "+$confmodule: Copie de $VLMCONF/$confsrc vers $destmodulepath/$confpath"
cp -f $VLMCONF/$confsrc $destmodulepath/$confpath || exit 1
echo 'OK !'

destmodulepath=$VLMJEUROOT/lib/phpcommon
confsrc=param.php
confpath=param.php
echo -n "+$confmodule: Copie de $VLMCONF/$confsrc vers $destmodulepath/$confpath"
cp -f $VLMCONF/$confsrc $destmodulepath/$confpath || exit 1
echo 'OK !'


