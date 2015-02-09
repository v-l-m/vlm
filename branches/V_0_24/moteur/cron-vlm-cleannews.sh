#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

LOG=$VLMLOG/$(date +%Y%m%d_%H%M)-$1-cronvlm-cleannews.log

#FIXME should be a global setup
export LOGFILE_MAX_AGE=7

cd $VLMJEUROOT/moteur
nice -1 $VLMPHPPATH notify/clean_news.php $* >> $LOG 2>&1

# Purge des anciens logs
#===8<===
cd $VLMLOG
[ $(pwd) == "$VLMLOG" ] && find . -name "*-cronvlm-cleannews.log" -mtime +$LOGFILE_MAX_AGE -exec rm -f {} \;
[ $(pwd) == "$VLMLOG" ] && find . -name "*-cronvlm-feed.log" -mtime +$LOGFILE_MAX_AGE -exec rm -f {} \;
[ $(pwd) == "$VLMLOG" ] && find . -name "*-cronvlm-feedresults.log" -mtime +$LOGFILE_MAX_AGE -exec rm -f {} \;
[ $(pwd) == "$VLMLOG" ] && find . -name "*-cronvlm-notify.log" -mtime +$LOGFILE_MAX_AGE -exec rm -f {} \;
#===8<===

