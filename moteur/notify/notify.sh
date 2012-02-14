#!/bin/bash
#VLMRACINE=/base/de/vlm #A configurer normalement dans le crontab

source $VLMRACINE/conf/conf_script || exit 1

LOG=$VLMLOG/$(date +%Y%m%d_%H%M)-$1-vlmnotify.log

#export VLMPHPPATH="/usr/bin/php --define extension=vlmc.so --define include_path=.:/usr/share/php:/home/vlmtest/svn/trunk/lib/phpcommon"

media=$1

$VLMPHPPATH $VLMJEUROOT/moteur/notify/$media.php >> $LOG 2>&1

