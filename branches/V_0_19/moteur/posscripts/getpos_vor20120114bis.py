#!/usr/bin/env python
# -*- coding: utf-8 -*-

import urllib
import sys, zipfile, os
import time
import xml.etree.ElementTree as ElementTree

def vlm_get_tmp():
    if os.environ.has_key('VLMTEMP'):
        return os.environ['VLMTEMP']
    else :
        return '.'
    
def unzip_file_into_dest(src, dest):
    zfobj = zipfile.ZipFile(src)
    for name in zfobj.namelist():
        outfile = open(dest, 'wb')
        outfile.write(zfobj.read(name))
        outfile.close()


#page = urlopen("http://volvooceanrace.geovoile.com/2011/shared/event/static.xml")
vlmidrace = 20120114
starttime = 1327219200
basefilename = "vor%d" % vlmidrace
vlmtmp = vlm_get_tmp()
statichwz = os.path.join(vlmtmp, basefilename+".static.tmp.hwz")
staticxml = os.path.join(vlmtmp, basefilename+".static.xml")
updatehwz = os.path.join(vlmtmp, basefilename+".update.tmp.hwz")
updatexml = os.path.join(vlmtmp, basefilename+".update.xml")

urllib.urlretrieve("http://volvooceanrace.geovoile.com/2011/shared/leg3/event/static.hwz", statichwz)
unzip_file_into_dest(statichwz, staticxml)

#<factors coord="1000" speed="10" distance="10" timecode="1" coef="1000"/>
#  Indique les facteurs de conversions des coordonnées
#<boat id="26" name="CAMPER Emirates Team NZ" sail="4" nbhulls="1" hullcolor="FFFFFF" trackcolor="FFFFFF" coef="1000">
#<virtualboat id="126" classid="1" boatid="26"/>
# correspondance id technique <=> numéro de dossard

tree = ElementTree.parse(staticxml)
boats = {}
for outline in tree.findall(".//boat"):
  boats[int(outline.attrib['id'])] = outline.attrib

#page = urlopen("http://volvooceanrace.geovoile.com/2011/leg2/shared/event/update.xml")
urllib.urlretrieve("http://volvooceanrace.geovoile.com/2011/shared/leg3/event/update.hwz", updatehwz)
unzip_file_into_dest(updatehwz, updatexml)

#parse
tree = ElementTree.parse(updatexml)

#Un peu de doc: 
#<reports><report  id="208" date="2011/11/27 01:02:11Z"><v i="121" st="" d="5993" l="5993" s="211" c="93" o="-431"/>...
#id = id du report
#date = date de la publication
#i = id (dossard+100)
#st = status (DNF, ARV, STA (at start), "" => en course)
#d = distance from finish (en 10ème de miles)
#l = difference of distance to finish from nearest best opponent (en 10ème de miles)
#s = speeed (10ème de noeuds)
#c = cap / heading
#o = offset de temps entre la date de publication et la mesure en secondes
for outline in tree.findall(".//track"):
  l = outline.text.split(';')
  lat, lon, t = 0, 0, starttime
  rid = int(outline.attrib['id'])
  id = -1200-rid
  for i in l:
    tup = i.split(',')
    lat += int(tup[0])
    lon += int(tup[1])
    t += int(tup[2])
    if time.time() - t < 48*3600:
        #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
        print "%d|0|%d|%d|%s|BAR|%f|%f|0.|0." % (vlmidrace,int(t), id, boats[rid]['name'].encode('utf-8'), lat/1000., lon/1000.)
        #print time.gmtime(int(t))

