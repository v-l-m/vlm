#!/bin/bash
###
### Script to build VLM Challenge 2022
### Should be run once as all races are completed

source $VLMRACINE/conf/conf_script || exit 1

$VLMPHPPATH $VLMRACINE/vlmcode/moteur/BuildVLMChallenge2022.php