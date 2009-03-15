#!/bin/bash
#VLMRACINE=/base/de/vlm #A configurer normalement fans le crontab
source $VLMRACINE/conf/conf_script || exit 1

[ -f $VLMTEMP/cronvlm.$1.lock ] && exit
touch $VLMTEMP/cronvlm.$1.lock

LOG=$VLMTEMP/$(date +%Y%m%d_%H:%M)-$1-cronvlm.log
export LOGFILE_MAX_AGE=7


cd $VLMJEUROOT/moteur
echo -e "\n" >> $LOG
echo  "******************* starting the engine ********************" >> $LOG
date >> $LOG
echo "************************************************************" >> $LOG
nice -10 $VLMPHPPATH moteur.php $* >> $LOG 2>&1

#maupiti & r16212.ovh.net sont les serveurs ou le moteur est susceptible d'envoyer un mail
#FIXME : faire une option de conf ?
[ $(hostname) != "maupiti.actilis" -a $(hostname) != "r16212.ovh.net" ] && grep CROSSED $LOG | sed 's/.*player //g' | sed 's/ CROSSED.*$//g' | while read idusers ; do
        sed -n "/user $idusers:/,/DONE/p"  $LOG >$VLMTEMP/CC-$idusers.log
        mail -s "COAST CROSSING : BOAT $idusers" vlm@virtual-winds.com -- -F "VLM-ENGINE" -f "vlm@virtual-winds.com" <$VLMTEMP/CC-$idusers.log
        rm -f $VLMTEMP/CC-$idusers.log
done

rm -f $VLMTEMP/cronvlm.$1.lock
gzip -9 $LOG

# Purge des anciens logs
#===8<===
cd $VLMTEMP
[ $(pwd) == "$VLMTEMP" ] && find . -name "*--cronvlm.log.gz" -mtime +$LOGFILE_MAX_AGE -exec rm -f {} \;
#===8<===

