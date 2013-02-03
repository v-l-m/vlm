#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

#Route de la decouverte
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_dec20121003.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php

