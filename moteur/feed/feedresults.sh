#!/bin/bash
#VLMRACINE=/base/de/vlm #A configurer normalement dans le crontab

source $VLMRACINE/conf/conf_script || exit 1

LOG=$VLMLOG/$(date +%Y%m%d_%H%M)-cronvlm-feedresults.log

#export VLMPHPPATH="/usr/bin/php --define extension=vlmc.so --define include_path=.:/usr/share/php:/home/vlmtest/svn/trunk/lib/phpcommon"

$VLMPHPPATH $VLMJEUROOT/moteur/feed/results.events.php >> $LOG 2>&1
