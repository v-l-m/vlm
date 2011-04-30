#!/bin/bash

if test "$1" = ""; then
  echo "Ce script s'appelle avec le nom de la version de destination et le step souhaité"
  echo " Exemple : ./runupgrade.sh 0.17 0"
  exit 0
fi

VLMRELEASE=$1
step=$2
upfile=$VLMJEUROOT/hosting/scripts/upgrades.d/up.$VLMRELEASE.$step.sh
lockfile=$VLMTEMP/vlmup.$VLMRELEASE.$step.lock
logfile=$VLMTEMP/vlmup.$VLMRELEASE.$step.log

echo -n "Looking for $VLMRACINE/conf/conf_script"
source $VLMRACINE/conf/conf_script || exit 1
echo " : OK"

echo "Upgrading scripts"
$VLMRACINE/scripts/maj_scripts.sh

if [ -f $lockfile ]; then
    echo "LOCKFILE $lockfile found. Back to manual upgrade !"
    exit 0
fi

if [ -f $upfile ]; then
    touch $lockfile
    echo "Running upgrade script $upfile"
    bash  $upfile > $logfile 
else
    echo "Did not find $upfile"
fi
