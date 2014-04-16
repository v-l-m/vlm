#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_ag2r20140406.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php

