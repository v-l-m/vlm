#!/bin/bash

#do what can be done before maintenance mode
$VLMRACINE/scripts/maj_module.sh base
$VLMJEUROOT/base/scripts/dump-alive.sh $VLMTEMP/vlmdump-alive.pre.sql
$VLMJEUROOT/base/scripts/dump-history.sh $VLMTEMP/vlmdump-history.pre.sql


