#!/bin/bash

PATHBD=/home/spenot/gshhs

for RESOL in c l i h f; do
    echo Nettoyage
        cd bd
        nice -10 rm -r bd_$RESOL
        rm $RESOL*.dat
        cd ..
done

