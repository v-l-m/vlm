#!/bin/bash
#VLMRACINE=/base/de/vlm #A configurer normalement dans le crontab
source $VLMRACINE/conf/conf_script || exit 1

echo "Running php script $1"

$VLMPHPPATH $1

