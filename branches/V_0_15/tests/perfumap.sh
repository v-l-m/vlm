#!/bin/sh

res=$1
baseurl="http://trunk.vlmtest.paparazzia.info/mercator.img.php?idraces=9981&lat=-55&long=-70.5&maparea=8&drawwind=no&tracks=off&maptype=compas&ext=right&wp=4&x=800&y=600&proj=mercator&fullres=$res"
basecmd="wget --quiet -O /dev/null"

echo "testing $res with $baseurl"

for i in {1..100}
do
   $basecmd "$baseurl"
done

