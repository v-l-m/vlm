#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

# VlmTransat 2012
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_kor20120707.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
# SDC 2012
##nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_sdc20120311.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
