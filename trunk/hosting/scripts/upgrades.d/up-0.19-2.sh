#!/bin/bash

$VLMRACINE/scripts/maj_module.sh externals
$VLMRACINE/scripts/maj_module.sh medias
$VLMRACINE/scripts/maj_module.sh site
$VLMRACINE/scripts/maj_module.sh jvlm
$VLMRACINE/scripts/maj_module.sh clip_gshhs

rm $VLMTEMP/cronvlm*.lock
