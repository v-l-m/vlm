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
    vlmboatidfirst = 3000
    return {'vlmid' : -vlmboatidfirst-int(rid)}
    
vlmidrace = 20150919
vlmusernameprefix = "MiniTrst - "
basefilename = "Moqueica%d" % vlmidrace
raceBaseUrl = "http://minitransat.geovoile.com/2015/_elements/data/race/"
print raceBaseUrl+"leg1.static.hwz?v=" + str(int(time.time()))
geotree = gp.GeovoileTree(raceBaseUrl+"leg1.static.hwz?v=" + str(int(time.time())), basefilename)
coordfactor = geotree.factors()

boats = geotree.boats()

geotreeLive = gp.GeovoileTree(raceBaseUrl+"leg1.live.hwz?v=" + str(int(time.time())), basefilename+"live")
geotreeUpdate = gp.GeovoileTree(raceBaseUrl+"leg1.update.hwz?v=" + str(int(time.time())), basefilename+"update")

for rid in boats.keys() :
    bb = baseboat(int(rid))
    boats[rid]['boatid'] = rid
    boats[rid]['vlmid'] = -bb['vlmid']
    boats[rid]['vlmboatname'] = "%03d - %s" % (rid, boats[rid]['name'])
    boats[rid]['vlmusername'] = "%s%03d" % (vlmusernameprefix, rid)
    print "insert into users (idusers,boattype,boatname,username,engaged) values (%d,'boat_figaro2','%s','%s',20150919);"%( +-boats[rid]['vlmid'], boats[rid]['vlmboatname'].replace("'","").encode('utf8'),boats[rid]['vlmusername'].encode('utf8'))

for track in geotreeUpdate.tracks(tagid='id',liveData=0):
      #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
     rid, t, lat, lon = track
     
     if time.time() - t < 48*3600 and t < time.time():
     
         print "%d|0|%d|%d|%s|BAR|%f|%f|0.|0." % (vlmidrace, t, -boats[rid]['vlmid'], boats[rid]['name'].encode('utf8'), lat/coordfactor, lon/coordfactor)


for track in geotreeLive.tracks(tagid='id',liveData=1):
      #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
     rid, t, lat, lon = track
     
     if time.time() - t < 48*3600 and t < time.time():
     
         print "%d|0|%d|%d|%s|BAR|%f|%f|0.|0." % (vlmidrace, t, -boats[rid]['vlmid'], boats[rid]['name'].encode('utf8'), lat/coordfactor, lon/coordfactor)
