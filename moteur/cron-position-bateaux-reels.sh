#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

#VDG 2016
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_vdg_2016.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php

#KJV 2016
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_Joyon_JV2016.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php

#coville
$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_coville_2016.php > $VLMTEMP/coville.json
python $VLMRACINE/vlmcode/moteur/posscripts/getpos_coville_2016.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
#New-York Vendée 2016
#nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_NewYorkVendee.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php

#Pellet VDG 2016
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_Pellet_VDG2016.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
