#!/bin/sh

. $VLMRACINE/conf/conf_script || exit 1

TILESCACHEDIR=$VLMCACHE/gshhstiles

SEAFILE=sea.png
FIXEDTILESDIR=f
CHECKFILE=.lastcheck
HARDLINKLIMIT=64000

if [ ! -d ${TILESCACHEDIR} ]; then
    echo "You must configure TILESCACHEDIR in clean_tilescache.sh" >&2
    exit 2
fi

cd ${TILESCACHEDIR}

emptysum="40d1d7d1e1b9bcaae0d69a5900dabf85"

sea=0
nblinks=0
if [ ! -d f ]; then
    mkdir ${FIXEDTILESDIR}
    echo "Creating fixed dir"
else
    if [ -f ${FIXEDTILESDIR}/${SEAFILE} ]; then
	sea=1
	nblinks=`ls -l ${FIXEDTILESDIR}/${SEAFILE} | awk '{ print $2}'`
    fi
fi

findmod=""
if [ -f ${CHECKFILE} ]; then
    findmod="-newer ${CHECKFILE}"
fi

find . -type f ${findmod} -print | grep -v ${SEAFILE} | while read tilename ; do
    echo "Checking $tilename"
    echo "$emptysum  $tilename" | md5sum -c --status
    if [ $? -eq 0 ]; then
        echo "Is empty!"
        if [ "$sea" -eq "0" ]; then
            mv $tilename ${FIXEDTILESDIR}/${SEAFILE}
            sea=1
        fi
	if [ $nblinks -lt $HARDLINKLIMIT ]; then
	    nblinks=`expr $nblinks + 1`
            ln -f ${FIXEDTILESDIR}/${SEAFILE} $tilename
	else
	    tstamp=`date +"%s"`
	    mv ${FIXEDTILESDIR}/${SEAFILE} ${FIXEDTILESDIR}/${tstamp}-${SEAFILE}
	    cp ${FIXEDTILESDIR}/${tstamp}-${SEAFILE} ${FIXEDTILESDIR}/${SEAFILE}
	    nblinks=2
	    echo "limit reached, storing ${SEAFILE} as ${tstamp}-${SEAFILE}"
	fi
	ln -f ${FIXEDTILESDIR}/${SEAFILE} $tilename
	echo "$nblinks hard links on ${SEAFILE}"
    fi
done

touch ${CHECKFILE}
