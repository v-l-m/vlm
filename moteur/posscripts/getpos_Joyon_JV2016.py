#!/usr/bin/env python
# -*- coding: utf-8 -*-

import urllib
import sys, zipfile, os
import time
#import xml.etree.ElementTree as ElementTree
import json
import requests
import getposlib as gp
import sys, time

#Generate with basedatas
def baseboat(rid):
    vlmboatidfirst = 3000
    return {'vlmid' : -vlmboatidfirst-int(rid)}
    
vlmidrace = 20150919
vlmusernameprefix = "NYVendee - "
basefilename = "NYVendee%d" % vlmidrace
#URL http://imocaoceanmasters-nyvendee.geovoile.com/2016/_elements/data/race/tracker.tracks.hwz?v=1464551980
raceBaseUrl = "http://trimaran_idec.geovoile.com/julesverne/2016/_elements/data/race/"
print raceBaseUrl+"tracker.tracks.hwz?v=" + str(int(time.time()))

gp.unzipurl(raceBaseUrl+"tracker.tracks.hwz?v=" + str(int(time.time())),basefilename)
vlmtmp = gp.vlm_get_tmp()

with open(os.path.join(vlmtmp,basefilename+".static.tmp.xml")) as data_file:    
    res = json.load(data_file)

Tracks = res['tracks']


for track in Tracks:
  if track['id'] != 0:
    #print track
    Time = -1
    lat = -1
    lon = -1
    realid = -4000-track['id']
    realname = "NY Vendée - %d" % track['id']
    speed = 0
    heading  = 0
    for Pos in track['loc']:
      print Pos
      if Time==-1:
        Time=Pos[0]
        lat = Pos[1] /100000.0
        lon = Pos[2] / 100000.0
      else:
        Time+=Pos[0]
        lat += Pos[1] /100000.0
        lon += Pos[2] / 100000.0
      print("%d|1|%d|%d|%s|%s|%f|%f|%f|%f\n" % (vlmidrace, Time, realid, realname,realname, lat, lon, speed, heading))
  # epoch = pos['t'] * 60
  # lat = float(pos['locs'][0]['A'])
  # lon = float(pos['locs'][0]['B'])
  # heading = float(pos['locs'][0]['D'])
  # speed =  float(pos['locs'][0]['I'])
  # print("%d|1|%d|%d|%s|SpinDrift|%f|%f|%f|%f\n" % (vlmidrace, epoch, realid, realname, lat, lon, speed, heading))
