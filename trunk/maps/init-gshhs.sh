#/bin/bash

#pour recuperer le VLMJEUROOT
source $VLMRACINE/conf/conf_script

destpath=$VLMGSHHS
gshhsfile=gshhs+wdbii_2.2.0.zip
if test -n "$VLMGSHHSURL" ; then
    urlgshhs=$VLMGSHHSURL/$gshhsfile
else    
    urlgshhs=http://www.ngdc.noaa.gov/mgg/shorelines/data/gshhs/version2.2.0/$gshhsfile
fi

mkdir -p $destpath
if test -e $destpath/gshhs_f.b ; then
    echo "+initGshhs: Les fichiers gshhs2 existent !"
    exit 0
else 
    echo "+initGshhs ; Downloading gshhs2 files from $urlgshhs to $destpath"
    rm -Rf $VLMTEMP/$gshhsfile
    wget --output-document=$VLMTEMP/$gshhsfile $urlgshhs
    unzip -u -j $VLMTEMP/$gshhsfile -d $destpath/
    mv $VLMTEMP/$gshhsfile $destpath/
fi        


