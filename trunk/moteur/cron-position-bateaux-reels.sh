#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

# G3
# nice -1 $VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/g3pos.php
# Sodebo
#nice -1 $VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/sodebopos.php
# BP5
nice -1 $VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/bp5pos2011.php
# KAWA !
#nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_kawa20091108.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
#nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_kawa20091110.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
# BWR 2011
#nice -1 $VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/bwrpos2011.php
# VOR 2011
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_vor20111211.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
# Back to 2011
#nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_b2b20111205.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
# SHYR 2011
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_shyr111226.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
