#!/usr/bin/env python
# -*- coding: utf-8 -*-

#importe le xml du VDG 2008 à postériori
#ne renvoi que le premier (michel desjoyaux)
#http://tracking.vendeeglobe.org/commun/vglobe_class.xml

import urllib2
import time
import xml.etree.ElementTree as ElementTree
urlopen = urllib2.urlopen

page = open("vglobe_class.xml")


data = page.readlines()
data = ' '.join(data)
tree = ElementTree.fromstring(data)
boats = {}

#paramètrage
offset = 0 #offset pour pouvoir décaler dans le temps présent
vlmidrace = 20081109
vlmid = -163

for outline in tree.findall("./poll"):
    t = int(outline.attrib['d']) + offset
    for b in outline.findall("./s"):
        r = b.attrib['r']
        dt = int(outline.attrib['t'])*60
        if r == "1":
            positions = b.findall("./p")
            nbp = len(positions)
            if nbp == 0 :
                continue
            step = dt / nbp
            for p in positions :
                nbp -= 1
                tt = t - nbp * step
                lon = float(p.attrib['x'])/1000.
                if lon > 180. :
                    lon -= 360.
                if lon < -180. :
                    lon += 360
                lat = float(p.attrib['y'])/1000.
                #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
                print "%d|0|%d|%d|FOO|BAR|%f|%f|0.|0." % (vlmidrace, int(tt), vlmid,  lat, lon)

