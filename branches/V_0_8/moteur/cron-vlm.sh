#!/bin/bash
[ -f /tmp/cronvlm.$1.lock ] && exit
touch /tmp/cronvlm.$1.lock

LOG=/tmp/$(date +%Y%m%d_%H:%M)-$1-cronvlm.log

#VLMRACINE=/base/de/vlm #A configurer normalement fans le crontab
source $VLMRACINE/conf/conf_script || exit 1

cd $VLMJEUROOT/moteur
echo -e "\n" >> $LOG
echo  "******************* starting the engine ********************" >> $LOG
date >> $LOG
echo "************************************************************" >> $LOG
nice -10 $VLMPHPPATH moteur.php $* >> $LOG 2>&1

#maupiti & r16212.ovh.net sont les serveurs ou le moteur est susceptible d'envoyer un mail
#FIXME : faire une option de conf ?
[ $(hostname) != "maupiti.actilis" -a $(hostname) != "r16212.ovh.net" ] && grep CROSSED $LOG | sed 's/.*player //g' | sed 's/ CROSSED.*$//g' | while read idusers ; do
        sed -n "/user $idusers:/,/DONE/p"  $LOG >/tmp/CC-$idusers.log
        mail -s "COAST CROSSING : BOAT $idusers" vlm@virtual-winds.com -- -F "VLM-ENGINE" -f "vlm@virtual-winds.com" </tmp/CC-$idusers.log
        rm -f /tmp/CC-$idusers.log
done

rm -f /tmp/cronvlm.$1.lock
gzip -9 $LOG

