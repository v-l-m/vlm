#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

#VOR 2014
#nice -1 $VLMRACINE/vlmcode/moteur/posscripts/GetVOR2014|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php

# l'hermione 2015
#nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_hermione.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php

# Minitransat
#nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_SpinDrift.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
#nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_IDEC.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php

