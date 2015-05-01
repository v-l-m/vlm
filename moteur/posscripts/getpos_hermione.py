#!/usr/bin/env python
# -*- coding' : 'utf-8 -*-

import requests #pour l'injection http
import time
import json

url ="http://services.arcgis.com/d3voDfTFbHOCRwVR/arcgis/rest/services/Derniere_Position_Hermione/FeatureServer/0/query?f=json&returnGeometry=true&spatialRel=esriSpatialRelIntersects&geometry=%7B\"xmin\"%3A-20037508.342788905%2C\"ymin\"%3A-65904617.28368805%2C\"xmax\"%3A20037508.342788905%2C\"ymax\"%3A14245416.087448012%2C\"spatialReference\"%3A%7B\"wkid\"%3A102100%2C\"latestWkid\"%3A3857%7D%7D&geometryType=esriGeometryEnvelope&inSR=102100&outFields=*&outSR=102100"
#first = "http://www.whatusea.com/embed/70441830a8f41ba763c26b6e9f28ac81"
#second= "http://www.whatusea.com/positions/get/?assetId=1078&isSharingMode=1"
vlmidrace = 20150506
realid = -2410
realname = "L'Hermione"

# headers = {
    # 'Host' : 'www.whatusea.com',
    # 'User-Agent' : 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:37.0) Gecko/20100101 Firefox/37.0',
    # 'Accept' : 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    # 'Accept-Language' : 'fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3',
   # 'Accept-Encoding' : 'gzip, deflate',
    # 'Referer' :'http://www.hermione.com/voyage/localiser-l-hermione/',
    # 'Connection' : 'keep-alive'
# }


s = requests.Session()
#s.headers.update(headers)

# q = s.get(first)
# s.headers.update(
  # {
   # 'Accept' : 'application/json, text/javascript, */*; q=0.01',
# Accept-Encoding: gzip, deflate
   # 'X-Requested-With' : 'XMLHttpRequest',
   # 'Referer' : 'http://www.whatusea.com/embed/70441830a8f41ba763c26b6e9f28ac81'
  # }
  # )
# q = s.get(second)
q=s.get(url)
#print (q.text)
#print type(q)
#print type(q.json)
res = json.loads(q.text)
feat = res['features'][0]
attr = feat['attributes']
epoc = attr['Date_Position']/1000
lat = attr['Latitude']
lon = attr['Longitude']
speed = attr['Vitesse']
heading = attr['Direction']
  
# ts = time.strptime(row['received_datestamp'], "%Y-%m-%d %H:%M:%S")
# epoc = int(time.mktime((ts.tm_year, ts.tm_mon, ts.tm_mday, ts.tm_hour, ts.tm_min, ts.tm_isdst, ts.tm_wday, ts.tm_yday, 0)))
#print "%d|0|%d|%d|%s|BAR|%f|%f|0.|0." % (vlmidrace, t, -boats[rid]['vlmid'], boats[rid]['name'].encode('utf8'), lat/coordfactor, lon/coordfactor)
#print(row['received_datestamp'])
print("%d|1|%d|%d|%s|HER|%f|%f|%f|%f\n" % (vlmidrace, epoc, realid, realname, lat, lon, speed, heading))

