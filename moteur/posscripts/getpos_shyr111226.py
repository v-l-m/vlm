#!/usr/bin/env python
# -*- coding: utf-8 -*-

import urllib2
import time
import xml.etree.ElementTree as ElementTree
urlopen = urllib2.urlopen

page = urlopen("http://rolexsydneyhobart.com/ge/getGEyachtracing.aspx")

nametoid = {
    "Wild Oats XI":1,
    "Investec Loyal":2,
    "Wild Thing":3,
    "Lahana":4,
    "Loki":5,
    "Hugo Boss":6,
    "Living Doll":7,
    "Calm":8,
    "Ragamuffin":9,
    "Ichi Ban":10,
    "Jazz":11,
    "Strewth":12,
    "Scarlet Runner":13,
    "Shogun":14,
    "Brindabella":15,
    "Southern Excellence":16,
    "Pretty Fly III":17,
    "Vamp":18,
    "Ffreefire 52":19,
    "Cougar II":20,
    "Optimus Prime":21,
    "Knee Deep":22,
    "Chutzpah":23,
    "Accenture Yeah Baby":24,
    "AFR Midnight Rambler":25,
    "Ocean Affinity":26,
    "Merit":27,
    "ColorTile":28,
    "Celestial":29,
    "NSC Mahligai":30,
    "St Jude":31,
    "Alacrity":32,
    "Minerva":33,
    "Victoire":34,
    "Balance":35,
    "Ella Bache":36,
    "Last Tango":37,
    "Deloitte As One":38,
    "Mondo":39,
    "Dump Truck":40,
    "Duende":41,
    "Patrice Six":42,
    "TSA Management":43,
    "The Goat":44,
    "LMR Solar":45,
    "Papillon":46,
    "Two True":47,
    "Mille Sabords":48,
    "Whistler":49,
    "Samurai Jack":50,
    "Lunchtime Legend":51,
    "Outrageous Fortune":52,
    "Dodo":53,
    "Carina":54,
    "The Banshee":55,
    "Kioni":56,
    "One For The Road":57,
    "Patrice IV":58,
    "Menace":59,
    "Quetzalcoatl":60,
    "Elektra":61,
    "Wave Sweeper":62,
    "Kiss Goodbye to MS":63,
    "Icefire":64,
    "Wasabi":65,
    "Sweethart":66,
    "L'ange De Milon":67,
    "Nutcracker":68,
    "Wild Rose":69,
    "Cadibarra 8":70,
    "Jazz Player":71,
    "Copernicus":72,
    "Martela":73,
    "Willyama":74,
    "Aurora":75,
    "Nemesis":76,
    "Flying Fish Arctos":77,
    "Shepherd Centre":78,
    "Eressea":79,
    "Natelle Two":80,
    "Illusion":81,
    "Bacardi":82,
    "Chancellor":83,
    "Not Negotiable":84,
    "She":85,
    "Alchemy III":86,
    "Fullynpushing":87,
    "Maluka Of Kermandie":88
    }


data = page.readlines()
data = ' '.join(data)
tree = ElementTree.fromstring(data)

prefix = "{http://earth.google.com/kml/2.0}"
offsetid = 1240

t = int(time.time()) #trop compliqué de récupérer les ts exacts pour cette course de 2 jours...
t = 1200*int(t/1200) #arrondi à 20min pour simplifier

for outline in tree.findall("./"+prefix+"Document/"+prefix+"Folder/"+prefix+"Placemark"):
  try :
      boatname = outline.find(prefix+"name").text
      boatid = -offsetid - nametoid[boatname]
      latitude = float(outline.find(prefix+"LookAt/"+prefix+"latitude").text)
      longitude = float(outline.find(prefix+"LookAt/"+prefix+"longitude").text)
  except :
      pass
  else :
      #print boatid, boatname, latitude, longitude
      #20091108|1|1257681600|-729|BT|Sébastien Josse - Jean François Cuzon|50.016000|-1.891500|85.252725|4651.600000
      print "111226|0|%d|%d|%s|BAR|%f|%f|0.|0." % (int(t), boatid, boatname, latitude, longitude)

