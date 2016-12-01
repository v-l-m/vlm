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
vlmusernameprefix = "JV - "
basefilename = "JV%d" % vlmidrace

vlmtmp = gp.vlm_get_tmp()

#recup du fichier de config


configUrl = "http://www.dolink.fr/dolink/trace/7038027"
configFile = os.path.join(vlmtmp,"config")
gp.geturl(configUrl,configFile,)

with open(configFile+".static.tmp.xml") as data_file:    
    livedata = json.load(data_file)

#gp.unzipurl(raceBaseUrl+"reports/"+reportfile,basefilename+"_rep")

#with open(os.path.join(vlmtmp,basefilename+"_rep.static.tmp.xml")) as data_file:    
#    reportdata = json.load(data_file)

#print livedata['tracks']
shape = livedata['shape']
#hist = reps['history'][0]
points = shape['points']
CurPos = points[0]

realid = -4082
realname = "VDG - 4082" 
speed = 0
heading  = 0
    #tr=track[11][0]
    #print tr
Time = time.time()
lat = (CurPos[0])
lon = (CurPos[1])

    
print("%d|1|%d|%d|%s|%s|%f|%f|%f|%f\n" % (vlmidrace, Time, realid, realname,realname, lat, lon, speed, heading))
    # epoch = pos['t'] * 60
  # lat = float(pos['locs'][0]['A'])
  # lon = float(pos['locs'][0]['B'])
  # heading = float(pos['locs'][0]['D'])
  # speed =  float(pos['locs'][0]['I'])
  # print("%d|1|%d|%d|%s|SpinDrift|%f|%f|%f|%f\n" % (vlmidrace, epoch, realid, realname, lat, lon, speed, heading))
