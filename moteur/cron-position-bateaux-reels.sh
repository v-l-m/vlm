#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

#The Bridge 2017
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_bridge_2017.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php

