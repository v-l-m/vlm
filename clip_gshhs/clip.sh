#!/bin/bash

RESOL=$1

PATHBD=/home/spenot/gshhs


    echo Resolution $RESOL
    echo Extraction de GSHHS
        ./readgshhs $RESOL
    echo Decoupe en carre de 45 x 45
        nice -10 python pygshhs1.py $RESOL
    echo Decoupe en carre de 15 x 15 - 5 x 5 - 1 x 1
        nice -10 python pygshhs2.py $RESOL
    echo Agglo
        ./read_bd $RESOL
    echo Copie
        cp $PATHBD/bd/bd_$RESOL/*.dat $PATHBD/bd
    echo Nettoyage
        cd bd
        nice -10 rm -r bd_$RESOL
        rm $RESOL*.dat
        cd ..


