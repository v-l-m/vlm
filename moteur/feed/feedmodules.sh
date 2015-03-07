#!/bin/bash
#VLMRACINE=/base/de/vlm #A configurer normalement dans le crontab

source $VLMRACINE/conf/conf_script || exit 1

LOG=$VLMLOG/$(date +%Y%m%d)-cronvlm-feedmodules.log

echo . >> $LOG
echo `date +%Y%m%d_%H%M` >> $LOG

$VLMPHPPATH $VLMJEUROOT/moteur/feed/modules.events.php $1 $2 >> $LOG 2>&1
