#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

LOG=$VLMLOG/$(date +%Y%m%d_%H%M)-$1-cronvlm-cleannews.log

nice -1 $VLMPHPPATH notify/clean_news.php $* >> $LOG 2>&1

# Purge des anciens logs
#===8<===
cd $VLMLOG
[ $(pwd) == "$VLMLOG" ] && find . -name "*-cronvlm-cleannews.log.gz" -mtime +$LOGFILE_MAX_AGE -exec rm -f {} \;
[ $(pwd) == "$VLMLOG" ] && find . -name "*-cronvlm-feed.log.gz" -mtime +$LOGFILE_MAX_AGE -exec rm -f {} \;
[ $(pwd) == "$VLMLOG" ] && find . -name "*-cronvlm-feedresults.log.gz" -mtime +$LOGFILE_MAX_AGE -exec rm -f {} \;
[ $(pwd) == "$VLMLOG" ] && find . -name "*-cronvlm-notify.log.gz" -mtime +$LOGFILE_MAX_AGE -exec rm -f {} \;
#===8<===

