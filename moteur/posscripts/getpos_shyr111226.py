#!/usr/bin/env python
# -*- coding: utf-8 -*-

import urllib2
import time
import xml.etree.ElementTree as ElementTree
urlopen = urllib2.urlopen

page = urlopen("http://rolexsydneyhobart.com/ge/getGEyachtracing.aspx")

data = page.readlines()
data = ' '.join(data)
tree = ElementTree.fromstring(data)
#boats = {}
#for outline in tree.findall(".//boat"):
#  boats[int(outline.attrib['id'])] = outline.attrib

#page = urlopen("http://volvooceanrace.geovoile.com/2011/shared/event/update.xml")
#data = page.readlines()
#data = ' '.join(data)

#tree = ElementTree.fromstring(data)
for outline in tree.findall(".//coordinates"):
  l = outline.text.split(';')
  lat, lon, t = 0, 0, 1320498000
  rid = int(outline.attrib['id'])
  id = -1200-rid
  for i in l:
    tup = i.split(',')
    lat += int(tup[0])
    lon += int(tup[1])
    t += int(tup[2])
  if time.time() - t < 48*3600:
    #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
    print "111226|0|%d|%d|%s|BAR|%f|%f|0.|0." % (int(t), id, boats[rid]['name'].encode('utf-8'), lat/1000., lon/1000.)

