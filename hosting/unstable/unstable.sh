#!/bin/sh

#crontab every minute
#git clone into ~/unstable
#manual symlinks to conf scripts and modules into site, jvlm, lib/phpcommon
#engine base, etc.. are still the main ones

#Wait, server maybe busy to run engines
sleep 30

#git pull
cd $VLMRACINE/unstable
git pull
