#!/bin/bash
###
### CRON to rebuild VLM Index
### Should be run hourly or so on a minute that is not a 5' crank time

source $VLMRACINE/conf/conf_script || exit 1

$VLMPHPPATH BuildVLMIndex.php
