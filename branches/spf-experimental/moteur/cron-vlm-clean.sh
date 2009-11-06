#!/bin/bash
##=================================================================
##   DOIT ETRE APPELE REGLIEREMENT A PARTIR DE LA 0.12           ##
##=================================================================
#VLMRACINE=/base/de/vlm #A configurer normalement fans le crontab

MAXMOTEURTIME=120

source $VLMRACINE/conf/conf_script || exit 1

LOG=$VLMLOG/$(date +%Y%m%d_%H%M)-$1-cronvlm-clean.log
export LOGFILE_MAX_AGE=7

# Si on trouve un lock, c'est qu'une instance du moteur est en fonctionnement
# Si sa date de derniere modif a plus de MAXMOTEURTIME secondes, c'est pas normal.
if [ -f $VLMTEMP/cronvlm-clean.$1.lock ] ; then

    find $VLMTEMP/ -name cronvlm-clean.$1.lock -mmin +$MAXMOTEURTIME | while read lockname ; do

         echo "=== LOCK FOUND : killing old engine instance ($lockname)" >> $LOG
         kill -SIGQUIT $(cat $lockname)

    done

fi

rm -f $VLMTEMP/cronvlm-clean.$1.lock
echo $$ > $VLMTEMP/cronvlm-clean.$1.lock

cd $VLMJEUROOT/moteur
echo -e "\n" >> $LOG
echo  "******************* starting the cleaner ********************" >> $LOG
date >> $LOG
echo "************************************************************" >> $LOG

ulimit -t $MAXMOTEURTIME

# to avoid stepping on a race engine run...
# note that it should be done during a "small" engine run, so
# no */5 in cron, more 3-58/5
sleep 15s

nice -1 $VLMPHPPATH dbcleanup.php $* >> $LOG 2>&1

rm -f $VLMTEMP/cronvlm-clean.$1.lock
gzip -9 $LOG

# Purge des anciens logs
#===8<===
cd $VLMLOG
[ $(pwd) == "$VLMLOG" ] && find . -name "*--cronvlm-clean.log.gz" -mtime +$LOGFILE_MAX_AGE -exec rm -f {} \;
#===8<===

