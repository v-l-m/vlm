#!/bin/sh

# init script, be sure to call this before cron and apache are started

#FIXER VLMRACINE ici s'il n'est pas défini comme variable d'environnement à ce stade.
#VLMRACINE=/path/to/vlmracine

source $VLMRACINE/conf/conf_script

GRIBPATH=$VLMGRIBS
LATEST=latest.grb

case "$1" in
  start)
    sudo -u $VLMGRIBUSER $VLMBIN/windserver $GRIBPATH/$LATEST
    exit 0
  ;;
  stop)
    ipcrm -S 0x2cc6ccad
    ipcrm -M 0x2cc6acb9
    ipcrm -M 0x2cc69e42
    exit 0
  ;;
  *)
  echo "Usage: $0 start|stop"
  exit 1
  ;;
esac

exit 0


