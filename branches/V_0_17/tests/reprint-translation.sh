#!/bin/bash
#VLMRACINE=/base/de/vlm #A configurer normalement fans le crontab
source $VLMRACINE/conf/conf_script || exit 1

$VLMPHPPATH reprint-translation.php > foo.txt
mv foo.txt ../site/includes/strings.inc

