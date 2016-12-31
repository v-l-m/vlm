#!/usr/bin/env python
# -*- coding: utf-8 -*-

import urllib
import sys, zipfile, os
import time
#import xml.etree.ElementTree as ElementTree
import json
#import requests
import getposlib as gp
import sys, time

#Generate with basedatas
def baseboat(rid):
    vlmboatidfirst = 3000
    return {'vlmid' : -vlmboatidfirst-int(rid)}
    
vlmidrace = 1682
vlmusernameprefix = "JV - "
basefilename = "JV%d" % vlmidrace

vlmtmp = gp.vlm_get_tmp()

#recup du fichier de config
configUrl = "http://trimaran-idec.geovoile.com/julesverne/2016/tracker/resources/versions/?v=" + str(int(time.time()))
configFile = os.path.join(vlmtmp,"config")
gp.geturl(configUrl,configFile,)
with open(configFile+".static.tmp.xml") as data_file:    
    conf = data_file.readlines()
conf = conf[0].split(",")
trackfile = conf[1].split(":")[1]
reportfile = conf[2].split(":")[1]

#URL http://imocaoceanmasters-nyvendee.geovoile.com/2016/_elements/data/race/tracker.tracks.hwz?v=1464551980
raceBaseUrl = "http://trimaran-idec.geovoile.com/julesverne/2016/tracker/resources/"
print raceBaseUrl

gp.geturl("http://testing.v-l-m.org/jvlm/pos1",trackfile)

with open(os.path.join(vlmtmp,trackfile+".static.tmp.xml")) as data_file:    
    livedata = json.load(data_file)

#gp.unzipurl(raceBaseUrl+"reports/"+reportfile,basefilename+"_rep")

#with open(os.path.join(vlmtmp,basefilename+"_rep.static.tmp.xml")) as data_file:    
#    reportdata = json.load(data_file)

#print livedata['reports']
reps = livedata['reports']
hist = reps['history'][0]
Tracks = hist['lines']
track = Tracks[0]
#print Tracks
    
#for track in Tracks:
  #print track
if track[0] != 0:
    Time = -1
    lat = -1
    lon = -1
    realid = -3902
    realname = "JV - %d" % int(track[0])
    speed = track[8]
    heading  = track[7]
    tr=track[17][0]
    #print tr
    Time = tr[0]
    lon = tr[2]
    lat = tr[1]

    print("%d|1|%d|%d|%s|%s|%f|%f|%f|%f\n" % (vlmidrace, Time, realid, realname,realname, lat, lon, speed, heading))
    print("%d|1|%d|%d|%s|%s|%f|%f|%f|%f\n" % (20161106, Time, -4081, realname,realname, lat, lon, speed, heading))
    

