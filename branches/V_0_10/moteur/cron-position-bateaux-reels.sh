#!/bin/bash

#VLMRACINE=/base/de/vlm #A configurer normalement fans le crontab
source $VLMRACINE/conf/conf_script || exit 1


# Ce script doit etre executable.
if [ -x /opt/php/bin/php ]; then
    PHP=/opt/php/bin/php
else
    PHP=php
fi


=======
# VOR : finished
# $PHP $VLMRACINE/vlmcode/moteur/posscripts/VolvoPos.php


