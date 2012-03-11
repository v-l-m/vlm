#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

# VOR 2011
#nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_vor20120318.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
# SDC 2011
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_sdc20120311.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
