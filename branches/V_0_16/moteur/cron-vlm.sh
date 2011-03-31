#!/bin/bash
##=================================================================
##   DOIT ETRE APPELE TOUTES LES MINUTES A COMPTER DE V0.9.8     ##
##=================================================================
#VLMRACINE=/base/de/vlm #A configurer normalement dans le crontab

MAXMOTEURTIME=120

source $VLMRACINE/conf/conf_script || exit 1

LOG=$VLMLOG/$(date +%Y%m%d_%H%M)-$1-cronvlm.log
export LOGFILE_MAX_AGE=7

# Si on trouve un lock, c'est qu'une instance du moteur est en fonctionnement
# Si sa date de derniere modif a plus de MAXMOTEURTIME secondes, c'est pas normal.
if [ -f $VLMTEMP/cronvlm.$1.lock ] ; then

    find $VLMTEMP/ -name cronvlm.$1.lock -mmin +$MAXMOTEURTIME | while read lockname ; do

         echo "=== LOCK FOUND : killing old engine instance ($lockname)" >> $LOG
         kill -SIGQUIT $(cat $lockname)

    done

fi

rm -f $VLMTEMP/cronvlm.$1.lock
echo $$ > $VLMTEMP/cronvlm.$1.lock

cd $VLMJEUROOT/moteur
echo -e "\n" >> $LOG
echo  "******************* starting the engine ********************" >> $LOG
date >> $LOG
echo "************************************************************" >> $LOG

ulimit -t $MAXMOTEURTIME

nice -1 $VLMPHPPATH moteur.php $* >> $LOG 2>&1

# Voir Option MAIL_FOR_COASTCROSSING dans conf_script
[ "$MAIL_FOR_COASTCROSSING" == true ] && grep CROSSED $LOG | sed 's/.*player //g' | sed 's/ CROSSED.*$//g' | while read idusers ; do
        sed -n "/user $idusers:/,/DONE/p"  $LOG >$VLMTEMP/CC-$idusers.log
#sendmail syntax        mail -s "COAST CROSSING : BOAT $idusers" vlm@virtual-winds.com -- -F "VLM-ENGINE" -f "vlm@virtual-winds.com" <$VLMTEMP/CC-$idusers.log
#mailx syntax
	mail -s "COAST CROSSING : BOAT $idusers" -r "VLM-ENGINE <vlm@virtual-winds.com>" vlm@virtual-winds.com <$VLMTEMP/CC-$idusers.log 
        rm -f $VLMTEMP/CC-$idusers.log
done

rm -f $VLMTEMP/cronvlm.$1.lock
gzip -9 $LOG

# Purge des anciens logs
#===8<===
cd $VLMLOG
[ $(pwd) == "$VLMLOG" ] && find . -name "*--cronvlm.log.gz" -mtime +$LOGFILE_MAX_AGE -exec rm -f {} \;
#===8<===

