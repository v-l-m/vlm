#!/bin/bash

#maintenance
$VLMRACINE/scripts/maintenance.sh

#base
$VLMJEUROOT/base/scripts/dump-alive.sh $VLMTEMP/vlmdump-alive.sql 
$VLMJEUROOT/base/scripts/runupgrade.sh $VLMRELEASE

rm -f $VLMGSHHS/gshhs_f.b
$VLMRACINE/scripts/maj_module.sh maps

$VLMRACINE/scripts/maj_module.sh moteur
$VLMRACINE/scripts/maj_module.sh lib/phpcommon
$VLMRACINE/scripts/maj_module.sh lib/vlm-c
#after this, we need to restart apache
