#/bin/bash

#pour recuperer le VLMJEUROOT
source $VLMRACINE/conf/conf_script

destpath=$VLMGSHHS
gshhsfile=gshhs_2.1.0.zip
urlgshhs=http://www.ngdc.noaa.gov/mgg/shorelines/data/gshhs/version2.1/$gshhsfile

mkdir -p $destpath
if test -e $destpath/gshhs_f.b ; then
    echo "+initGshhs: Les fichiers gshhs2 existent !"
    exit 0
else 
    echo "+initGshhs ; Downloading gshhs2 files from $urlgshhs to $destpath"
    rm -Rf $VLMTEMP/$gshhsfile
    wget --output-document=$VLMTEMP/$gshhsfile $urlgshhs
    unzip -j $VLMTEMP/$gshhsfile -d $destpath/
fi        


