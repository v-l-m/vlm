#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

# Vdg 2012
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_vdg20121110.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_arc20121125.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
