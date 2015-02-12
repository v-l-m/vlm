#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

#VOR 2014
nice -1 $VLMRACINE/vlmcode/moteur/posscripts/GetVOR2014|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
# RDR 2014
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_b2b20141231.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php

