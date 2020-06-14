#!/bin/bash
##=================================================================
##   DOIT ETRE APPELE REGULIEREMENT dans un cron quotidien       ##
##=================================================================
#VLMRACINE=/base/de/vlm #A configurer normalement dans le crontab

source $VLMRACINE/conf/conf_script || exit 1

LOG=$VLMLOG/$(date +%Y%m%d_%H%M)-$1-cronvlm-Daily-clean.log
#FIXME should be a global setup
export LOGFILE_MAX_AGE=7


#purge des fichiers cachés non accédé depuis 30 jours.
cd $VLMRACINE/cache
nice /usr/bin/find ./gribtiles ./gshhstiles ./minimaps ./racemaps ./tinymaps ./tracks -type f -atime +30 -delete;
nice /usr/bin/find ./gribtiles/* ./gshhstiles/* ./minimaps/* ./racemaps/* ./tinymaps/* ./tracks/* -type d -empty -delete | grep -v "No such file"
