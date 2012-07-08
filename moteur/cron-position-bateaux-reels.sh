#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

# VlmTransat 2012
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_kor20120707.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
#TFV 2012
nice -1 python $VLMRACINE/vlmcode/moteur/posscripts/getpos_tfv20120708.py|$VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/getpos_py_wrapper.php
