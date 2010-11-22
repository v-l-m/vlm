#!/bin/bash

BD=./bd/poly-f-1.dat
N=2
X0=0
X1=1
Y0=0
Y1=1
FORMAT=png

./tiles_g $N 0 0 $BD 00.$FORMAT
./tiles_g $N 0 1 $BD 01.$FORMAT
./tiles_g $N 1 0 $BD 10.$FORMAT
./tiles_g $N 1 1 $BD 11.$FORMAT
