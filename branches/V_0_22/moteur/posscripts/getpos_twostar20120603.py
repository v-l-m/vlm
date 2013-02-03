#!/usr/bin/env python
# -*- coding: utf-8 -*-

#YellowBrick based http://yb.tl/links/twostar2012

import urllib
import sys, os
import time, calendar
import csv
import getposlib as gp
import sys, time
    
vlmidrace = 20120603
vlmusernameprefix = "TS2012_"
basefilename = "ts%d" % vlmidrace
url = "http://yb.tl/twostar2012-expedition.txt"
vlmtmp = gp.vlm_get_tmp()
pathcsv = os.path.join(vlmtmp, basefilename+".tmp.csv")
urllib.urlretrieve(url, pathcsv)

csvReader = csv.reader(open(pathcsv, 'rb'), delimiter=',', quotechar='|')
for row in csvReader:
    #1202212115
    try: 
        tup = (2012, int(row[3][2:4]), int(row[3][4:6]), int(row[3][6:8]), int(row[3][8:10]), 0, 0, 0, 0)
        print "%d|0|%d|%d|FOO|BAR|%f|%f|0.|0." % (vlmidrace, calendar.timegm(tup), -1460-int(row[0]), float(row[1]), float(row[2]))
    except :
        pass

