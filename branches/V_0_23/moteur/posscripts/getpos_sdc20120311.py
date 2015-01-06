#!/usr/bin/env python
# -*- coding: utf-8 -*-

import urllib
import sys, zipfile, os
import time
import xml.etree.ElementTree as ElementTree


import getposlib as gp
import sys, time

#Generate with basedatas
def baseboat(rid):
    vlmboatidfirst = 1430
    vlmboatridfirst = 59
    return {'vlmid' : -vlmboatidfirst-int(rid)+vlmboatridfirst}
    
vlmidrace = 20120311
vlmusernameprefix = "SDC2012_"
basefilename = "sdc%d" % vlmidrace
geotree = gp.GeovoileTree("http://lasolidaireduchocolat.geovoile.com/2012/shared/data/leg1.static.hwz", basefilename)

coordfactor = geotree.factors()

timezero = 1331483880 #16:38 (origin of tracks) #geotree.timezero() #FIXME

boats = geotree.boats()

geotree = gp.GeovoileTree("http://lasolidaireduchocolat.geovoile.com/2012/shared/data/leg1.update.hwz", basefilename+"update")

for rid in boats.keys() :
    bb = baseboat(int(rid))
    boats[rid]['boatid'] = rid
    boats[rid]['vlmid'] = -bb['vlmid']
    boats[rid]['vlmboatname'] = "%03d - %s" % (boats[rid]['sail'], boats[rid]['name'])
    boats[rid]['vlmusername'] = "%s%03d" % (vlmusernameprefix, rid)

if len(sys.argv) > 1 and sys.argv[1] == 'sqlusers' :
        gp.sqlusers(boats, vlmidrace)
        sys.exit();
if len(sys.argv) > 2 and sys.argv[1] == 'basedatas' :
        gp.basedatas(boats, int(sys.argv[2]))
        sys.exit()

for track in geotree.tracks(tagid='id'):
      #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
     rid, t, lat, lon = track
     t += timezero
     if time.time() - t < 48*3600 and t < time.time():
         print "%d|0|%d|%d|%s|BAR|%f|%f|0.|0." % (vlmidrace, t, -boats[rid]['vlmid'], boats[rid]['name'].encode('utf-8'), lat/coordfactor, lon/coordfactor)

