#!/usr/bin/env python
# -*- coding: utf-8 -*-

#YellowBrick based http://yb.tl/links/twostar2012

import urllib
import sys, os
import time, calendar
import getposlib as gp
import sys, time
import pprint

vlmfirstid = 1600    
vlmidrace = 20121125
vlmusernameprefix = "ARC2012_"
basefilename = "ts%d" % vlmidrace
vlmtmp = gp.vlm_get_tmp()

ybtree = gp.YellowBrickTree("http://yb.tl/Flash/arc2012/TeamSetup/", basefilename)

boats = ybtree.boats()

ybtree = gp.YellowBrickTree("http://yb.tl/Flash/arc2012/AllPositions/", basefilename, "pos")

tracks = ybtree.tracks()

for p in tracks :
      t = p[1]
      rid = p[0]
      lat = p[2]
      lon = p[3]
      if time.time() - t < 12*3600:
          #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
          boat = boats[rid]
          vlmid = vlmfirstid +rid
          print("%d|0|%d|%d|%s|%s|%f|%f|0.|0." % (vlmidrace, t, -vlmid, boat['name'].encode('utf8'), boat['skipper']['name'].encode('utf8'), lat, lon))
          #print time.gmtime(int(t))


