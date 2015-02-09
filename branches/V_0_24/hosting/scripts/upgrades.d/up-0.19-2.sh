#!/bin/bash

$VLMRACINE/scripts/maj_module.sh externals
$VLMRACINE/scripts/maj_module.sh medias
$VLMRACINE/scripts/maj_module.sh site
$VLMRACINE/scripts/maj_module.sh jvlm
rm -f $VLMGSHHS/rivers-f-1.dat
$VLMRACINE/scripts/maj_module.sh clip_gshhs
$VLMRACINE/scripts/maj_module.sh guest_map

rm $VLMTEMP/cronvlm*.lock
