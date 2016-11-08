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
basefilename = "JV_Coville"
realid = -4080
realname = 'Thomas Coville'
vlmtmp = gp.vlm_get_tmp()

#recup du fichier de config
#configUrl = "http://gitana-team.geovoile.com/vendeeglobe/2016/tracker/resources/versions/?v=" + str(int(time.time()))
#configFile = os.path.join(vlmtmp,"config")
#gp.geturl(configUrl,configFile,)
with open(os.path.join(vlmtmp,"./coville.json")) as data_file:    
    data = json.load(data_file)
#conf = conf[0].split(",")
#trackfile = conf[1].split(":")[1]
#reportfile = conf[2].split(":")[1]
time = data['ts']
pollings = data['pollings']
track=pollings['p']

#print Tracks
    
for points in track:
    point = points['locs'][0]
    lat = float(point['A'])
    lon = float(point['B'])
    speed = float(point['I'])
    heading = float(point['D'])
    
print("%d|1|%d|%d|%s|%s|%f|%f|%f|%f\n" % (vlmidrace, time, realid, realname,realname, lat, lon, speed, heading))
  # epoch = pos['t'] * 60
  # lat = float(pos['locs'][0]['A'])
  # lon = float(pos['locs'][0]['B'])
  # heading = float(pos['locs'][0]['D'])
  # speed =  float(pos['locs'][0]['I'])
  # print("%d|1|%d|%d|%s|SpinDrift|%f|%f|%f|%f\n" % (vlmidrace, epoch, realid, realname, lat, lon, speed, heading))
