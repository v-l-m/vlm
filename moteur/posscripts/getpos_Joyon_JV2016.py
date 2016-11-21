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
    
vlmidrace = 1681
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

gp.unzipurl(raceBaseUrl+"tracks/"+trackfile,basefilename)

with open(os.path.join(vlmtmp,basefilename+".static.tmp.xml")) as data_file:    
    livedata = json.load(data_file)

#gp.unzipurl(raceBaseUrl+"reports/"+reportfile,basefilename+"_rep")

#with open(os.path.join(vlmtmp,basefilename+"_rep.static.tmp.xml")) as data_file:    
#    reportdata = json.load(data_file)

#print livedata['tracks']
reps = livedata['tracks'][0]
#hist = reps['history'][0]
Tracks = reps['loc']

print Tracks

PrevTime = 0
PrevLat = 0
PrevLon = 0
for track in Tracks:
  #print track
    realid = -3902
    realname = "VDG - 3902" 
    speed = 0
    heading  = 0
    #tr=track[11][0]
    #print tr
    Time = track[0]+PrevTime
    lon = (track[2]/100000.)+PrevLon
    lat = (track[1]/100000.)+PrevLat

    
    print("%d|1|%d|%d|%s|%s|%f|%f|%f|%f\n" % (vlmidrace, Time, realid, realname,realname, lat, lon, speed, heading))
    PrevTime = Time
    PrevLat = lat
    PrevLon = lon
  # epoch = pos['t'] * 60
  # lat = float(pos['locs'][0]['A'])
  # lon = float(pos['locs'][0]['B'])
  # heading = float(pos['locs'][0]['D'])
  # speed =  float(pos['locs'][0]['I'])
  # print("%d|1|%d|%d|%s|SpinDrift|%f|%f|%f|%f\n" % (vlmidrace, epoch, realid, realname, lat, lon, speed, heading))
