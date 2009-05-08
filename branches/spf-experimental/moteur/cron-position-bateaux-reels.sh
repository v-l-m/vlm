#!/bin/bash

#VLMRACINE=/base/de/vlm #A configurer normalement fans le crontab
source $VLMRACINE/conf/conf_script || exit 1

# VOR
$VLMPHPPATH $VLMJEUROOT/moteur/posscripts/VolvoPos.php


