#!/bin/bash

# WARNING: this script depends on cnvgrib [1]  and windserver (from vlm-c)
# [1] <http://www.nco.ncep.noaa.gov/pmb/codes/GRIB2/>
# It generates an interim grib to be retrieved until the full version is available.

# PATH=/path-to-cnvgrib-and-windserver-if-needed:$PATH

GRIBPATH=/path/to/gribfiles/grib
TMPGRIBPATH=/path/to/gribfiles/tmpgrib

PREFIX=gfs_NOAA
TIME_THRESHOLD=09
MAX_TIME=24
INTERIM_NAME=gfs_interim-${TIME_THRESHOLD}.grb

if [ $MAX_TIME -lt 12 ]; then
    echo "MAX_TIME must be > 12"
    echo "Fix the script"
    exit 0
fi

HH=$1
DAT=`date '+%Y%m%d'`
LOG=log.txt

updated=0
mkdir $TMPGRIBPATH/$DAT$HH

cd $TMPGRIBPATH/$DAT$HH
rm -Rf  ${PREFIX}*
declare -i retry=1

if [ $MAX_TIME -lt 100 ]; then
    allindexes=`seq -w 0 3 ${MAX_TIME}`
else
    firstindexes=`seq -w 0 3 99`
    lastindexes=`seq -w 102 3 $MAX_TIME`
    allindexes=`echo $firstindexes" "$lastindexes`
fi

# Now get the individual grib entry convert in grib1 and merge
for TSTAMP in `echo $allindexes` ; do
    GRIBFILE=gfs.t${HH}z.master.grbf${TSTAMP}.10m.uv.grib2
    let retry=1
    while [ $retry -gt 0 ]; do
	wget --waitretry 600 -nc -c ftp://ftpprd.ncep.noaa.gov/pub/data/nccf/com/gfs/prod/gfs.$DAT$HH/$GRIBFILE >>$LOG 2>&1
        let retry=$?
	if [ $retry -gt 0 ] ; then 
	    sleep 30
	fi
    done
    echo  $DAT $GRIBFILE downloaded... >> $LOG 2>&1
    cnvgrib -g21 $GRIBFILE $GRIBFILE.grib1
    echo $GRIBFILE converted >> $LOG 2>&1
    cat $GRIBFILE.grib1 >> ${PREFIX}-${DAT}${HH}.grb
    if [ $TSTAMP -gt $TIME_THRESHOLD ]; then
	if [ ! -f $GRIBPATH/$INTERIM_NAME ]; then
	    cp ${PREFIX}-${DAT}${HH}.grb $GRIBPATH/$INTERIM_NAME
	fi
	# we change the weather now
	windserver $PREFIX-${DAT}${HH}.grb >> $LOG 2>&1
	updated=1
    fi
done

rm $GRIBPATH/$PREFIX*${HH}.grb

# we change the weather now (if not done yet)
if [ $updated -eq 0 ]; then
    windserver $PREFIX-${DAT}${HH}.grb >> $LOG 2>&1
fi
# then cleanup
mv $PREFIX-${DAT}${HH}.grb $GRIBPATH/
rm -f $GRIBPATH/$INTERIM_NAME
rm $GRIBPATH/latest.24.grb
ln -s ${GRIBPATH}/$PREFIX-${DAT}${HH}.grb $GRIBPATH/latest.24.grb
mv $LOG $GRIBPATH/
rm -Rf $TMPGRIBPATH/$DAT$HH

