#!/usr/bin/env python
# -*- coding: utf-8 -*-

import urllib
import sys, zipfile, os
import time
import xml.etree.ElementTree as ElementTree
import getposlib

vlmidrace= 20121110
basefilename = "vdg%d" % vlmidrace
vlmtmp = getposlib.vlm_get_tmp()
updatexml = os.path.join(vlmtmp, basefilename+".update.xml")
now = int(time.time())

urllib.urlretrieve("http://tracking2012.vendeeglobe.org/data/class1.xml?%d" % now, updatexml)

tree = ElementTree.parse(updatexml)
vlmids = {
    'benedetto' : 1516,
    'thomson' : 1517,
    'lecleach' : 1518,
    'boissieres' : 1519,
    'stamm' : 1520,
    'debroc' : 1521,
    'wavre' : 1522,
    'gabart' : 1523,
    'sanso' : 1524,
    'lecam' : 1525,
    'dick' : 1526,
    'beyou' : 1527,
    'depavant' : 1528,
    'burton' : 1529,
    'guillemot' : 1530,
    'golding' : 1531,
    'davies' : 1532,
    'delamotte' : 1533,
    'riou' : 1534,
    'gutek' : 1535
}

boats = {}
timezero = 1352548920

rid = 0
for b in tree.findall(".//s"):
    #<s n1="Alessandro Di Benedetto" b="Team Plastique" p="benedetto" t="558a42" nat="it"/>
    rid += 1
    boat = b.attrib
    boat['name'] = b.attrib['b'].encode('utf-8')
    boat['skippers'] = b.attrib['n1'].encode('utf-8')
    boat['vlmid'] = vlmids[b.attrib['p']]
    boats[rid] = boat

t = timezero
for outline in tree.findall(".//poll"):
  t += int(outline.attrib['d'])*60
  rid = 0
  #print(outline.attrib)
  for c in outline.findall("./c"):
      
      rid += 1
      #<c p="17" r="18" a="0" x="-2.53230" y="45.47560" v="7.3" d="16.7" c="210"/>
      if 'x' not in c.attrib:
          continue
      lat = float(c.attrib['y'])
      lon = float(c.attrib['x'])
      cap = spd = 0.
      if 'k' in outline.keys() and int(outline.attrib['k']) == 1:
          try :
              cap = float(c.attrib['c'])
              spd = float(c.attrib['v'])
          except :
              pass
              
          
      if time.time() - t < 48*3600:
          #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
          boat = boats[rid]
          
          print "%d|0|%d|%d|%s|%s|%f|%f|0.|0." % (vlmidrace, t, -boat['vlmid'], boat['name'], boat['skippers'], lat, lon)
          #print time.gmtime(int(t))

