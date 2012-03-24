#!/usr/bin/env python
# -*- coding: utf-8 -*-

import urllib
import time
import getposlib as gp
import sys, time

#Generate with basedatas
def baseboat(rid):
    vlmboatidfirst = 1440
    return {'vlmid' : -vlmboatidfirst-int(rid), 'rid': rid}
    
vlmidrace = 20120324
vlmusernameprefix = "SNST900_"
basefilename = "snst%d" % vlmidrace
url = "http://www.sat-view.fr/comptes/snst/traces/positions.csv"

csvReader = gp.CsvPositions(url, basefilename)

for row in csvReader.csv:
    if len(row) < 2 or row[0][:2] != "ST":
        continue
    line = baseboat(int(row[0][2:]))
    line['time'] = time.mktime(time.strptime(row[1], "%d/%m/%Y %H:%M:%S"))
    line['lat'] = float(row[2])
    line['long'] = float(row[3])
    
    try:
        print "%d|0|%d|%d|FOO|BAR|%f|%f|0.|0." % (vlmidrace, line['time'], line['vlmid'], line['lat'], line['long'])
    except :
        pass

