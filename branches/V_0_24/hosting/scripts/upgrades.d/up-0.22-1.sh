#!/bin/bash

#maintenance
$VLMRACINE/scripts/maintenance.sh

#base
$VLMJEUROOT/base/scripts/dump-alive.sh $VLMTEMP/vlmdump-alive.sql 
$VLMJEUROOT/base/scripts/runupgrade.sh $VLMRELEASE

$VLMRACINE/scripts/maj_module.sh moteur
$VLMRACINE/scripts/maj_module.sh lib/phpcommon
