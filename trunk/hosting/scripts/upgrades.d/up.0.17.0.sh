#!/bin/bash

$VLMRACINE/scripts/maj_module.sh base
$VLMJEUROOT/base/scripts/dump-alive.sh $VLMTEMP/vlmdump-alive.sql
$VLMJEUROOT/base/scripts/runupgrade.sh $VLMRELEASE

$VLMRACINE/scripts/maj_module.sh moteur

$VLMRACINE/scripts/maj_module.sh lib/vlm-c
/etc/init.d/apache2 restart

$VLMRACINE/scripts/maj_module.sh lib/phpcommon
$VLMRACINE/scripts/maj_module.sh site
$VLMRACINE/scripts/maj_module.sh externals
$VLMRACINE/scripts/maj_module.sh medias

