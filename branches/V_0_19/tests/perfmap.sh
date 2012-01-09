#!/bin/sh

for res in poly nopoly polyline
do
#echo "==testing $res"
time ./perfumap.sh $res
done

