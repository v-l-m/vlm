#!/bin/bash

#Commun aux scripts
source $VLMRACINE/conf/conf_script || exit 1


for i in `ls $VLMPOLARS` ; do
    if [ $i != polars.list ] ; then
        echo -n "$i"|sed 's/boat_\(.*\)\.csv$/\1/' >> $VLMPOLARS/polars.list.tmp
        echo ":$VLMPOLARS/$i" >> $VLMPOLARS/polars.list.tmp
    fi;
done ;

mv $VLMPOLARS/polars.list.tmp $VLMPOLARS/polars.list
echo 'Liste polaires OK !'

echo -n "+$confmodule: Mise à jour polarserver"
$VLMBIN/polarserver $VLMPOLARS/polars.list

echo "Mise à jour du des polaires OK!" 1>&2
