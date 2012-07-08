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
    vlmboatidfirst = 1469
    sptoid = {
      'rfw' : 1,
      'foncia' : 2,
      'rothschild' : 3,
      'spindrift' : 4,
      'oman' : 5
      }
    return {'vlmid' : -vlmboatidfirst-sptoid[rid]}
    
vlmidrace = 20120707
vlmusernameprefix = "KOR2012_"
basefilename = "kor%d" % vlmidrace
addvisotree = gp.AddvisoTree("http://krysoceanrace.addviso.com/data/positions.xml", basefilename)
coordfactor = addvisotree.factors()

#Datafile use fr locale ! Stupid !
import locale
locale.setlocale(locale.LC_ALL, 'fr_FR.UTF-8')

timezero = addvisotree.timezero() #GMT Offset 1341680400 #addvisotree.timezero() #FIXME 07/07/2012 - 17:00 GMT

boats = addvisotree.boats()

for rid in boats.keys() :
    bb = baseboat(rid)
    boats[rid]['boatid'] = rid
    boats[rid]['vlmid'] = -bb['vlmid']
    boats[rid]['vlmboatname'] = "%s - %s" % (rid[0:3].upper(), boats[rid]['name'])
    boats[rid]['vlmusername'] = "%s%s" % (vlmusernameprefix, rid[0:3].upper())

for bid in boats:
      #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
    t = timezero
    if time.time() - t < 48*3600 and t < time.time():
        print "%d|0|%d|%d|%s|BAR|%f|%f|0.|0." % (vlmidrace, t, -boats[bid]['vlmid'], boats[bid]['name'].encode('utf8'), float(boats[bid]['lt'])/coordfactor, float(boats[bid]['ln'])/coordfactor)
        
#for bid in boats:
#    print("INSERT INTO users SET idusers = %d, username = '%s', boatname = '%s', engaged = %d ;" % (
#        -boats[bid]['vlmid'],
#        boats[bid]['vlmusername'],
#        boats[bid]['vlmboatname'],
#        vlmidrace))

