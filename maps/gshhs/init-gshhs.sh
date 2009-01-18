#/bin/bash

gshhsfile=gshhs_1.10.zip
urlgshhs=http://www.ngdc.noaa.gov/mgg/shorelines/data/gshhs/version1.10/$gshhsfile

echo "Ready to download gshhs files and install here"

wget $urlgshhs
unzip -j $gshhsfile -d ./