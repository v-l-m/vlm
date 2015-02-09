#!/bin/bash
#VLMRACINE=/base/de/vlm #A configurer normalement fans le crontab
source $VLMRACINE/conf/conf_script || exit 1

$VLMPHPPATH check-translation.php

