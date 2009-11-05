#!/bin/bash

source $VLMRACINE/conf/conf_script || exit 1

# G3
nice -1 $VLMPHPPATH $VLMRACINE/vlmcode/moteur/posscripts/g3pos.php

