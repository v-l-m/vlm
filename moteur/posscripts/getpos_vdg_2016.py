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
    
vlmidrace = 20161106
vlmusernameprefix = "VDG - "
basefilename = "VDG%d" % vlmidrace

vlmtmp = gp.vlm_get_tmp()

#recup du fichier de config
configUrl = "http://gitana-team.geovoile.com/vendeeglobe/2016/tracker/resources/versions/?v=" + str(int(time.time()))
configFile = os.path.join(vlmtmp,"config")
gp.geturl(configUrl,configFile,)
with open(configFile+".static.tmp.xml") as data_file:    
    conf = data_file.readlines()
conf = conf[0].split(",")
trackfile = conf[1].split(":")[1]
reportfile = conf[2].split(":")[1]

#URL http://imocaoceanmasters-nyvendee.geovoile.com/2016/_elements/data/race/tracker.tracks.hwz?v=1464551980
raceBaseUrl = "http://gitana-team.geovoile.com/vendeeglobe/2016/tracker/resources/"
print raceBaseUrl

gp.unzipurl(raceBaseUrl+"live/"+trackfile,basefilename)

with open(os.path.join(vlmtmp,basefilename+".static.tmp.xml")) as data_file:    
    livedata = json.load(data_file)

#gp.unzipurl(raceBaseUrl+"reports/"+reportfile,basefilename+"_rep")

#with open(os.path.join(vlmtmp,basefilename+"_rep.static.tmp.xml")) as data_file:    
#    reportdata = json.load(data_file)

#print livedata['reports']
reps = livedata['reports']
hist = reps['history'][0]
Tracks = hist['lines']

#print Tracks
    
for track in Tracks:
  #print track
  if track[0] != 0:
    Time = -1
    lat = -1
    lon = -1
    realid = -4050-int(track[0])
    realname = "VDG - %d" % int(track[0])
    speed = track[7]
    heading  = track[6]
    tr=track[11][0]
    #print tr
    Time = tr[0]
    lat = tr[2]
    lon = tr[1]
    
    print("%d|1|%d|%d|%s|%s|%f|%f|%f|%f\n" % (vlmidrace, Time, realid, realname,realname, lat, lon, speed, heading))
  # epoch = pos['t'] * 60
  # lat = float(pos['locs'][0]['A'])
  # lon = float(pos['locs'][0]['B'])
  # heading = float(pos['locs'][0]['D'])
  # speed =  float(pos['locs'][0]['I'])
  # print("%d|1|%d|%d|%s|SpinDrift|%f|%f|%f|%f\n" % (vlmidrace, epoch, realid, realname, lat, lon, speed, heading))
