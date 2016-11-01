#!/bin/bash

#maintenance
$VLMRACINE/scripts/maintenance.sh

#base
$VLMJEUROOT/base/scripts/dump-alive.sh $VLMTEMP/vlmdump-alive.sql 
$VLMJEUROOT/base/scripts/runupgrade.sh $VLMRELEASE #do we need this for this release ?

#core
$VLMRACINE/scripts/maj_module.sh site
$VLMRACINE/scripts/maj_module.sh jvlm
