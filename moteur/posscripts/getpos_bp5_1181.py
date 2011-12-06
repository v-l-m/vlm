#!/usr/bin/env python
# -*- coding: utf-8 -*-

#in 2011, this script is just useful to get G3 tracks

import urllib
import time
import sys, zipfile, os, os.path
import xml.etree.ElementTree as ElementTree

def unzip_file_into_dir(file, dir):
    zfobj = zipfile.ZipFile(file)
    for name in zfobj.namelist():
        outfile = open("bp5.tmp.gl", 'wb')
        outfile.write(zfobj.read(name))
        outfile.close()

#get file
urllib.urlretrieve("http://banquepopulaire.geovoile.fr/julesverne/2011/data/reports/leg1.gvz?", "bp5.tmp.glz")
unzip_file_into_dir('bp5.tmp.glz', '.')

#parse
tree = ElementTree.parse('bp5.tmp.gl')

#date start : FIXME : timezero is currently on 7/12/2011 ???
timezero = int(tree.getroot().attrib['timezero'])

#matching realid and virtual id
#kboats = {1 : -5, 255 : -3} #BP5 is handled by another script.
kboats = {255 : -3}

#array of boats
boats = {}

#base informations
for outline in tree.findall("./boats/boat"):
  boats[int(outline.attrib['id'])] = outline.attrib
  boats[int(outline.attrib['id'])]['name'] = outline.find('name').text.encode('utf-8')
#  print boats[int(outline.attrib['id'])]

#location 
for outline in tree.findall(".//location"):
  id = int(outline.attrib['boatid'])
  if id not in kboats.keys():
      continue

  boats[id]['vid'] = kboats[id]

  boats[id]['lat'] = 0.
  boats[id]['lon'] = 0.
  boats[id]['t'] = timezero
  for p in outline.text.split(';'):
      lat, lon, t = p.split(',')
      boats[id]['lat'] += float(lat)/1000.
      boats[id]['lon'] += float(lon)/1000.
      boats[id]['t'] += int(t)

  #print boats[id]
  #if time.time() - boats[id]['t'] < 48*3600: #FIXME : HARDCODED
    #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
      print "1181|0|%d|%d|%s|BAR|%f|%f|0.|0." % (boats[id]['t'], boats[id]['vid'], boats[id]['name'], boats[id]['lat'], boats[id]['lon'])

