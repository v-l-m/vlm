#!/bin/bash

# WARNING: this script depends on cnvgrib [1]  and windserver (from vlm-c)
# [1] <http://www.nco.ncep.noaa.gov/pmb/codes/GRIB2/>
# It generates an interim grib to be retrieved until the full version is available.

# PATH=/path-to-cnvgrib-and-windserver-if-needed:$PATH

GRIBPATH=/path/to/gribfiles/grib
TMPGRIBPATH=/path/to/gribfiles/tmpgrib

GRIBURLPATH=http://www.example.com/grib
TMPGRIBURLPATH=http://www.example.com/grib

PREFIX=gfs_NOAA
TIME_THRESHOLD=09
MAX_TIME=24
INTERIM_NAME=gfs_interim-${TIME_THRESHOLD}.grb

HH=$1
DAT=`date '+%Y%m%d'`
LOG=log.txt

interim=0
updated=0

mkdir $TMPGRIBPATH/$DAT$HH

cd $TMPGRIBPATH/$DAT$HH
rm -Rf  ${PREFIX}*
declare -i retry=1

while [ $updated = 0 ]; do
    if [ $interim = 0 ]; then
	wget $TMPGRIBURLPATH/$INTERIM_NAME >>$LOG 2>&1
	if [ $? -eq 0 ]; then
	    $interim=1
	    windserver $INTERIM_NAME >>$LOG 2>&1
	fi
    fi
    wget $GRIBURLPATH/$PREFIX-${DAT}${HH}.grb >>$LOG 2>&1
    if [ $? -eq 0 ]; then
	$updated=1
	windserver $PREFIX-${DAT}${HH}.grb  >>$LOG 2>&1
    else
	sleep 10
    fi
done

rm $GRIBPATH/$PREFIX*${HH}.grb

mv $PREFIX-${DAT}${HH}.grb $GRIBPATH/
rm -f $GRIBPATH/$INTERIM_NAME
rm $GRIBPATH/latest.24.grb
ln -s ${GRIBPATH}/$PREFIX-${DAT}${HH}.grb $GRIBPATH/latest.24.grb
mv $LOG $GRIBPATH/
rm -Rf $TMPGRIBPATH/$DAT$HH

