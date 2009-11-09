#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

# G38
nice -1 $VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/g3pos.php
# KAWA !
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_kawacup20091108.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_kawacup20091110.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
