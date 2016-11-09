#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

#VDG 2016
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_vdg_2016.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php

#coville
$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_coville_2016.php > $VLMTEMP/coville.json
python $VLMRACINE/vlmcode/moteur/posscripts/getpos_coville_2016.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
#New-York Vendée 2016
#nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_NewYorkVendee.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
