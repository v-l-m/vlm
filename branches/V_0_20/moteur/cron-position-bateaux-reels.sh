#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

# VOR 2012
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_vor20120701.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
# TSR 2012
#nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_twostar20120603.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
