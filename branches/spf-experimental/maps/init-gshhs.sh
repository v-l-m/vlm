#/bin/bash

#pour récupérer le VLMJEUROOT
source $VLMRACINE/conf/conf_script

destpath=$VLMGSHHS
gshhsfile=gshhs_1.10.zip
urlgshhs=http://www.ngdc.noaa.gov/mgg/shorelines/data/gshhs/version1.10/$gshhsfile

mkdir -p $destpath
if test -e $destpath/gshhs_f.b ; then
    echo "+initGshhs: Les fichiers gshhs existent !"
    exit 0
else 
    echo "+initGshhs ; Downloading gshhs files from $urlgshhs to $destpath"
    rm -Rf $VLMTEMP/$gshhsfile
    wget --output-document=$VLMTEMP/$gshhsfile $urlgshhs
    unzip -j $VLMTEMP/$gshhsfile -d $destpath/
fi        


