#!/bin/bash

# WARNING: this script depends on cnvgrib [1]  and windserver (from vlm-c)
# [1] <http://www.nco.ncep.noaa.gov/pmb/codes/GRIB2/>
# It generates an interim grib to be retrieved until the full version is available.

source $VLMRACINE/conf/conf_script

PATH=$VLMBIN:$PATH

#GRIBPATH=/path/to/gribfiles/grib - fixe dans le conf_script
GRIBPATH=$VLMGRIBS

TMPGRIBPATH=$GRIBPATH/tmpgrib

PREFIX=gfs_NOAA
TIME_THRESHOLD=09
if [ ! -n "$VLM_GRIB_MAX_TIME" ]; then
    VLM_GRIB_MAX_TIME=24
fi

# Minimum grib file size for validity
minimumsize=300000
GRIB_MAX_TIME=$VLM_GRIB_MAX_TIME

LATEST=latest.grb
INTERIM_NAME=gfs_interim-${TIME_THRESHOLD}.grb
#NOAA_SERVICE_MAIN_URI=http://www.ftp.ncep.noaa.gov/data/nccf/com/gfs/prod
#NOAA_SERVICE_BACKUP_URI=http://nomads.ncep.noaa.gov/pub/data/nccf/com/gfs/prod
NOAA_SERVICE_MAIN_URI=http://nomads.ncep.noaa.gov
NOAA_SERVICE_BACKUP_URI=http://nomads.ncep.noaa.gov

if [ $GRIB_MAX_TIME -lt 12 ]; then
    echo "GRIB_MAX_TIME must be > 12"
    echo "Fix the script"
    exit 0
fi

if test "$1" = ""; then
  let "HH=( `date -u +%H`  + ( `date -u +%M` / 30 ) - 4 + 24 ) / 6 * 6 % 24"
  echo "No argument, auto-computing to HH=$HH"
else
  HH=$1
fi

DAT=`date '+%Y%m%d'`
LOG=log.txt

updated=0
mkdir -p $TMPGRIBPATH/$DAT$HH

cd $TMPGRIBPATH/$DAT$HH
rm -Rf  ${PREFIX}*
declare -i retry=1

# check if we have another instance running (FIXME kill & cleanup)
ps -d -o pid,ppid,command | grep $0 | grep -v $$ | while read scriptpid
do
        echo "Another instance of $0 is running under PID $scriptpid" >> $LOG 2>&1
done


if [ $GRIB_MAX_TIME -lt 100 ]; then
    allindexes=`seq  -f %03g 0 3 ${GRIB_MAX_TIME}`
else
    firstindexes=`seq -f %03g 0 3 99 `
    lastindexes=`seq -f %03g 102 3 $GRIB_MAX_TIME `
    allindexes=`echo $firstindexes" "$lastindexes`
fi

      
# Now get the individual grib entry convert in grib1 and merge
for TSTAMP in `echo $allindexes` ; do
    GRIBURL="gfs.t${HH}z.pgrb2full.0p50.f${TSTAMP}&lev_10_m_above_ground=on&var_UGRD=on&var_VGRD=on&leftlon=0&rightlon=360&toplat=90&bottomlat=-90&dir=%2Fgfs.$DAT$HH"
    GRIBFILE="gfs.t${HH}z.pgrb2full.0p50.f${TSTAMP}"
    rm -f $GRIBFILE
    let retry=1
    while [ $retry -gt 0 ]; do
      if [ $retry -gt 1 ] ; then 
        rm -f $GRIBFILE
        sleep 30
      fi
      
      wget --waitretry 600 -nc -c ${NOAA_SERVICE_MAIN_URI}/cgi-bin/filter_gfs_0p50.pl?file=$GRIBURL -O $GRIBFILE >>$LOG 2>&1
      #let retry=$?
      echo "wget returned $retry" >>$LOG
      
      # Check null size file
      if [ ! -s $GRIBFILE ] ; then
        rm -f $GRIBFILE
        let retry=1
      fi
      #check minimum size for file
      if [ -e "$GRIBFILE" ] ; then
        actualsize=$(wc -c "$GRIBFILE" | cut -f 1 -d ' ')
      else
        actualsize=0
      fi

      #Check for minimumsize
      if [ $actualsize -le $minimumsize ]; then
        # file too small
        echo "grib file too small $actualsize < $minimumsize bytes" >>$LOG
        rm -f $GRIBFILE
        let retry++
      else
        # file size looks good
        echo  $DAT $GRIBFILE downloaded... >> $LOG 2>&1
        cnvgrib -g21 $GRIBFILE $GRIBFILE.grib1
        echo $GRIBFILE converted >> $LOG 2>&1
        actualsize=$(wc -c "$GRIBFILE.grib1" | cut -f 1 -d ' ')
        # if [ $actualsize -ne 844908 ]; then
          # let retry++
          # echo "Invalid grib file size, retry #" $retry  >> $LOG 2>&1
          
        # else
          echo "Grib1 size ok. File complete"  >> $LOG 2>&1
          let retry=0
        # fi
      fi

      if [ $retry -eq 4 ]; then
        #Enough retries for this file pray for the best
        let retry=0
      fi
      
    done
    cat $GRIBFILE.grib1 >> ${PREFIX}-${DAT}${HH}.grb
    if [ $TSTAMP -gt $TIME_THRESHOLD ]; then
      if [ ! -f $GRIBPATH/$INTERIM_NAME ]; then
        cp ${PREFIX}-${DAT}${HH}.grb $GRIBPATH/$INTERIM_NAME
      fi
      # we change the weather now
      if [ $updated -eq 0 ]; then
        windserver $PREFIX-${DAT}${HH}.grb >> $LOG 2>&1
        updated=1
        
      fi
    fi
done

#archiving if needed
if [ "yes" = "$ARCHIVE_GRIB" ]; then
  if [ ! -d $GRIBPATH/archives ]; then
    mkdir $GRIBPATH/archives
  fi
  YEAR=`date '+%Y'`
  if [ ! -d $GRIBPATH/archives/${YEAR} ]; then
    mkdir $GRIBPATH/archives/${YEAR}
  fi
  YDAT=`date '+%Y/%m%d'`
  if [ ! -d $GRIBPATH/archives/${YDAT} ]; then
    mkdir $GRIBPATH/archives/${YDAT}
  fi
  for TSTAMP in 003 006 009 012 ; do
    GRIBFILE=gfs.t${HH}z.pgrb2full.0p50.f${TSTAMP}
    cp $GRIBFILE $GRIBPATH/archives/${YDAT}/$GRIBFILE.grib2
  done
fi

rm -f $GRIBPATH/$PREFIX*${HH}.grb

# we update the weather now 
windserver -update $PREFIX-${DAT}${HH}.grb >> $LOG 2>&1

# then cleanup
mv $PREFIX-${DAT}${HH}.grb $GRIBPATH/
rm -f $GRIBPATH/$INTERIM_NAME
rm -f $GRIBPATH/$LATEST
ln -s ${GRIBPATH}/$PREFIX-${DAT}${HH}.grb $GRIBPATH/$LATEST
rm -Rf $VLMRACINE/cache/gribtiles/*/ >> $LOG 2>&1 
mv $LOG $GRIBPATH/
rm -Rf $TMPGRIBPATH/$DAT$HH
date +%s >  $GRIBPATH/GribCacheIndex

